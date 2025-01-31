<?php

namespace App\Http\Controllers\Dr\Panel\Turn\Schedule\ScheduleSetting\BlockingUsers;

use App\Models\Dr\Doctor;
use Auth;
use App\Models\User;
use Illuminate\Http\Request;
use App\Models\Dr\SmsTemplate;
use App\Models\Dr\UserBlocking;
use Modules\SendOtp\App\Http\Services\MessageService;
use Modules\SendOtp\App\Http\Services\SMS\SmsService;
use Str;

class BlockingUsersController
{
 public function index(Request $request)
 {
  $blockedUsers = UserBlocking::with('user')->get();
  $messages = SmsTemplate::with('user')->latest()->get();
  $users = User::all(); // دریافت لیست همه کاربران
  if ($request->ajax()) {
   return response()->json(['blockedUsers' => $blockedUsers]);
  }
  return view('dr.panel.turn.schedule.scheduleSetting.blocking_users.index', compact('blockedUsers', 'messages', 'users'));
 }

 public function store(Request $request)
 {
  $doctorId = Auth::guard('doctor')->user()->id ?? Auth::guard('secretary')->user()->doctor_id ;

  try {
   $validated = $request->validate([
    'mobile' => 'required|exists:users,mobile', // بررسی وجود شماره موبایل
    'blocked_at' => 'required|date',
    'unblocked_at' => 'nullable|date|after:blocked_at',
    'reason' => 'nullable|string|max:255',
   ]);

   // بررسی اینکه آیا کاربر قبلاً مسدود شده است یا خیر
   $user = User::where('mobile', $validated['mobile'])->first();
   if (!$user) {
    return response()->json([
     'success' => false,
     'message' => 'خطا در ذخیره‌سازی کاربر. لطفاً دوباره تلاش کنید.',
     'error' => 'کاربری با این شماره موبایل وجود ندارد.',
    ], 422);
   }
   $isBlocked = UserBlocking::where('user_id', $user->id)->where('doctor_id', $doctorId)->exists();

   if ($isBlocked) {
    return response()->json([
     'success' => false,
     'message' => 'خطا در ذخیره‌سازی کاربر. لطفاً دوباره تلاش کنید.',
     'error' => 'این کاربر قبلاً مسدود شده است.',
    ], 422);
   }

   $blocking_user = UserBlocking::create([
    'user_id' => $user->id,
    'doctor_id' => $doctorId,
    'blocked_at' => $validated['blocked_at'],
    'unblocked_at' => $validated['unblocked_at'] ?? null,
    'reason' => $validated['reason'] ?? null,
    'status' => 1, // مقدار پیش‌فرض
   ]);

   // ارسال پیامک به کاربر
   $doctorName = Doctor::where('id', $doctorId)->first();
   $doctorName = $doctorName->first_name . " " . $doctorName->last_name;
   $message = "کاربر گرامی، شما توسط پزشک {$doctorName} مسدود شده‌اید. برای اطلاعات بیشتر با ما تماس بگیرید.";
   $messagesService = new MessageService(
    SmsService::create($message, $user->mobile)
   );
   $messagesService->send();
   $saveMessage = SmsTemplate::create([
    'doctor_id' => $doctorId,
    'user_id' => $user->id, // اضافه کردن user_id
    'identifier' => Str::random('11'),
    'title' => "مسدودی کاربر",
    'content' => $message,
   ]);
   if ($blocking_user && $saveMessage) {
    return response()->json([
     'success' => true,
     'message' => 'کاربر با موفقیت مسدود شد.',
     'blocking_user' => $blocking_user->load('user'), // لود کردن اطلاعات کاربر
    ]);
   }

  } catch (\Exception $e) {
   return response()->json([
    'success' => false,
    'message' => 'خطا در ذخیره‌سازی کاربر. لطفاً دوباره تلاش کنید.',
    'error' => $e->getMessage(),
   ], 500);
  }
 }
 public function sendMessage(Request $request)
 {
  $validated = $request->validate([
   'title' => 'required|string|max:255',
   'content' => 'required|string',
   'recipient_type' => 'required|in:all,blocked,specific',
   'specific_recipient' => 'nullable|exists:users,mobile',
  ]);
  

  try {
   $recipients = [];
   if ($validated['recipient_type'] === 'all') {
    $recipients = User::pluck('mobile')->toArray();
   } elseif ($validated['recipient_type'] === 'blocked') {
    $recipients = UserBlocking::with('user')->get()->pluck('user.mobile')->toArray();
   } elseif ($validated['recipient_type'] === 'specific') {
    $user = User::where('mobile', $validated['specific_recipient'])->first();
    if (!$user) {
     return response()->json([
      'success' => false,
      'message' => 'شماره موبایل وارد شده در سیستم وجود ندارد.',
     ], 422);
    }
    $recipients[] = $validated['specific_recipient'];
   }
   foreach ($recipients as $recipient) {
    $messagesService = new MessageService(
     SmsService::create($validated['content'], $recipient)
    );
    $messagesService->send();
   }
   $user = User::where('mobile',$validated['specific_recipient'])->first();
   SmsTemplate::create([
    'doctor_id' => Auth::guard('doctor')->user()->id ?? Auth::guard('secretary')->user()->doctor_id,
    'user_id' => $user->id,
    'title' => $validated['title'],
    'content' => $validated['content'],
    'type' => 'manual',
    'identifier' => uniqid(),
   ]);

   return response()->json(['success' => true, 'message' => 'پیام با موفقیت ارسال شد.']);
  } catch (\Exception $e) {
   return response()->json([
    'success' => false,
    'message' => 'خطا در ارسال پیام!',
    'error' => $e->getMessage(),
   ], 500);
  }
 }




 public function getMessages()
 {
  $messages = SmsTemplate::with('user')->latest()->get();

  return response()->json($messages);
 }


 public function updateStatus(Request $request)
 {
  try {
   $userBlocking = UserBlocking::findOrFail($request->id);
   $userBlocking->status = $request->status;
   $userBlocking->save();

   // ارسال پیامک به کاربر
   $user = $userBlocking->user;
   $doctor = $userBlocking->doctor; // فرض می‌کنیم رابطه Doctor تعریف شده است

   $doctorName = $doctor->first_name . ' ' . $doctor->last_name;
   $message = $request->status == 1
    ? "کاربر گرامی، شما توسط پزشک {$doctorName} مسدود شده‌اید. لطفاً جهت اطلاعات بیشتر با ما تماس بگیرید."
    : "کاربر گرامی، شما توسط پزشک {$doctorName} از حالت مسدودی خارج شدید. اکنون دسترسی شما فعال است.";

   $smsService = new MessageService(
    SmsService::create($message, $user->mobile)
   );
   $smsService->send();

   // ذخیره پیام در جدول SmsTemplate
   SmsTemplate::create([
    'doctor_id' => $doctor->id,
    'user_id' => $user->id, // اضافه کردن user_id
    'identifier' => Str::random(11),
    'title' => $request->status == 1 ? 'مسدودی کاربر' : 'رفع مسدودی',
    'content' => $message,
   ]);

   return response()->json([
    'success' => true,
    'message' => 'وضعیت با موفقیت به‌روزرسانی شد و پیام ارسال گردید.',
   ]);
  } catch (\Exception $e) {
   return response()->json([
    'success' => false,
    'message' => 'خطا در به‌روزرسانی وضعیت.',
    'error' => $e->getMessage(),
   ], 500);
  }
 }

 public function deleteMessage($id)
 {
  try {
   $message = SmsTemplate::findOrFail($id);
   $message->delete();

   return response()->json([
    'success' => true,
    'message' => 'پیام با موفقیت حذف شد.',
   ]);
  } catch (\Exception $e) {
   return response()->json([
    'success' => false,
    'message' => 'خطا در حذف پیام.',
    'error' => $e->getMessage(),
   ], 500);
  }
 }


 public function destroy($id)
 {
  try {
   $userBlocking = UserBlocking::findOrFail($id);
   $userBlocking->delete();

   return response()->json([
    'success' => true,
    'message' => 'کاربر با موفقیت حذف شد.',
   ]);
  } catch (\Exception $e) {
   return response()->json([
    'success' => false,
    'message' => 'خطا در حذف کاربر.',
   ], 500);
  }
 }


}
