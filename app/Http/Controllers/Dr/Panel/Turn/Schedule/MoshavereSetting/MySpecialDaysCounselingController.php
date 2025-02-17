<?php
namespace App\Http\Controllers\Dr\Panel\Turn\Schedule\MoshavereSetting;

use App\Models\Dr\DoctorCounselingConfig;
use Carbon\Carbon;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Dr\CounselingHoliday;
use Illuminate\Support\Facades\Auth;
use App\Models\Dr\CounselingAppointment;
use App\Models\Dr\CounselingDailySchedule;
use App\Models\Dr\DoctorCounselingWorkSchedule;
use Modules\SendOtp\App\Http\Services\MessageService;
use Modules\SendOtp\App\Http\Services\SMS\SmsService;

class MySpecialDaysCounselingController{
 public function mySpecialDays()
 {
  return view("dr.panel.turn.schedule.Counseling.my-special-days");
 }
 public function updateWorkSchedule(Request $request)
 {
  // اعتبارسنجی ورودی
  $request->validate([
   'date' => 'required|date',
   'work_hours' => 'required|json',
   'selectedClinicId' => 'nullable|string', // اضافه کردن فیلتر selectedClinicId
  ]);

  $date = $request->date;
  $workHours = json_decode($request->work_hours, true);
  $selectedClinicId = $request->input('selectedClinicId');

  // بررسی وجود ساعات کاری برای تاریخ مورد نظر در جدول جدید
  $specialWorkHoursQuery = CounselingDailySchedule::where('date', $date);

  // اگر selectedClinicId وجود دارد و برابر 'default' نیست، فیلتر را اعمال کنید
  if ($selectedClinicId && $selectedClinicId !== 'default') {
   $specialWorkHoursQuery->where('clinic_id', $selectedClinicId);
  }

  $specialWorkHours = $specialWorkHoursQuery->first();

  if ($specialWorkHours) {
   // اگر وجود داشت، بروزرسانی شود
   $specialWorkHours->update(['work_hours' => json_encode($workHours)]);
  } else {
   // در غیر این صورت، رکورد جدید اضافه شود
   CounselingDailySchedule::create([
    'doctor_id' => auth()->guard('doctor')->user()->id,
    'date' => $date,
    'work_hours' => json_encode($workHours),
    'clinic_id' => $selectedClinicId, // اضافه کردن clinic_id به رکورد جدید
   ]);
  }

  return response()->json([
   'status' => true,
   'message' => 'ساعات کاری با موفقیت بروزرسانی شد.'
  ]);
 }
 public function getHolidayStatus(Request $request)
 {
  // اعتبارسنجی ورودی
  $validated = $request->validate([
   'date' => 'required|date',
   'selectedClinicId' => 'nullable|string', // فیلتر selectedClinicId
  ]);

  // گرفتن شناسه دکتر
  $doctorId = Auth::guard('doctor')->id() ?? Auth::guard('secretary')->id();
  $selectedClinicId = $request->input('selectedClinicId');

  // بررسی تعطیلی پزشک
  $holidayRecord = CounselingHoliday::where('doctor_id', $doctorId)
   ->where(function ($query) use ($selectedClinicId) {
    // اعمال فیلتر کلینیک اگر وارد شده باشد
    if ($selectedClinicId && $selectedClinicId !== 'default') {
     $query->where('clinic_id', $selectedClinicId);
    }
   })
   ->first();

  $holidayDates = json_decode($holidayRecord->holiday_dates ?? '[]', true);
  $isHoliday = in_array($validated['date'], $holidayDates);

  // گرفتن نوبت‌های پزشک در تاریخ مشخص و کلینیک انتخاب‌شده
  $appointments = CounselingAppointment::where('doctor_id', $doctorId)
   ->where('appointment_date', $validated['date'])
   ->when($selectedClinicId && $selectedClinicId !== 'default', function ($query) use ($selectedClinicId) {
    $query->where('clinic_id', $selectedClinicId);
   })
   ->get();

  return response()->json([
   'status' => true,
   'is_holiday' => $isHoliday,
   'data' => $appointments
  ]);
 }
 public function rescheduleAppointment(Request $request)
 {
  // اعتبارسنجی ورودی‌ها
  $validated = $request->validate([
   'old_date' => 'required|date', // تاریخ قبلی نوبت
   'new_date' => 'required|date', // تاریخ جدید نوبت
   'selectedClinicId' => 'nullable|string', // کلینیک انتخابی
  ]);

  $doctorId = Auth::guard('doctor')->id() ?? Auth::guard('secretary')->id();
  $selectedClinicId = $request->input('selectedClinicId');

  try {
   // پیدا کردن تمام نوبت‌های آن تاریخ با فیلتر کلینیک
   $appointments = CounselingAppointment::where('doctor_id', $doctorId)
    ->where('appointment_date', $validated['old_date'])
    ->when($selectedClinicId && $selectedClinicId !== 'default', function ($query) use ($selectedClinicId) {
     $query->where('clinic_id', $selectedClinicId);
    })
    ->get();

   if ($appointments->isEmpty()) {
    return response()->json([
     'status' => false,
     'message' => 'هیچ نوبتی برای این تاریخ یافت نشد.',
    ], 404);
   }

   // بررسی ساعات کاری پزشک برای تاریخ جدید
   $selectedDate = Carbon::parse($validated['new_date']);
   $dayOfWeek = strtolower($selectedDate->format('l'));
   // بررسی ساعات کاری پزشک برای تاریخ جدید
   $workHours = DoctorCounselingWorkSchedule::where('doctor_id', $doctorId)
    ->where('day', $dayOfWeek)
    ->when($selectedClinicId === 'default', function ($query) {
     $query->whereNull('clinic_id');
    })
    ->when($selectedClinicId && $selectedClinicId !== 'default', function ($query) use ($selectedClinicId) {
     $query->where('clinic_id', $selectedClinicId);
    })
    ->first();

   // دیباگ برای بررسی داده‌های بازگشتی از ساعات کاری
   if (!$workHours) {
    return response()->json([
     'status' => false,
     'message' => 'ساعات کاری یافت نشد.',
     'debug' => [
      'doctor_id' => $doctorId,
      'clinic_id' => $selectedClinicId,
      'day' => $dayOfWeek,
     ]
    ], 400);
   }


   // لیست شماره‌های موبایل کاربران برای ارسال پیامک
   $recipients = [];

   foreach ($appointments as $appointment) {
    $appointment->appointment_date = $validated['new_date'];
    $appointment->save();

    if ($appointment->patient && $appointment->patient->mobile) {
     $recipients[] = $appointment->patient->mobile;
    }
   }

   // تبدیل تاریخ‌ها به شمسی
   $oldDateJalali = \Morilog\Jalali\Jalalian::fromDateTime($validated['old_date'])->format('Y/m/d');
   $newDateJalali = \Morilog\Jalali\Jalalian::fromDateTime($validated['new_date'])->format('Y/m/d');

   // ارسال پیامک به کاربران
   if (!empty($recipients)) {
    $messageContent = "کاربر گرامی، نوبت شما از تاریخ {$oldDateJalali} به تاریخ {$newDateJalali} تغییر یافت.";
    foreach ($recipients as $recipient) {
     $user = User::where('mobile', $recipient)->first();
     $userFullName = $user ? ($user->first_name . " " . $user->last_name) : "کاربر گرامی";

     $messagesService = new MessageService(
      SmsService::create(
       100252,
       $recipient,
       [$userFullName, $oldDateJalali, $newDateJalali, 'به نوبه']
      )
     );
     $messagesService->send();
    }
   }

   return response()->json([
    'status' => true,
    'message' => 'نوبت‌ها با موفقیت جابجا شدند و پیامک ارسال گردید.',
    'total_recipients' => count($recipients),
   ]);
  } catch (\Exception $e) {
   return response()->json([
    'status' => false,
    'message' => 'خطا در جابجایی نوبت‌ها.',
    'error' => $e->getMessage(),
   ], 500);
  }
 }
 public function getAppointmentsCountPerDay(Request $request)
 {
  try {
   // دریافت شناسه پزشک یا منشی
   $doctorId = Auth::guard('doctor')->id() ?? Auth::guard('secretary')->id();
   $selectedClinicId = $request->input('selectedClinicId'); // کلینیک انتخابی

   // استخراج تعداد نوبت‌های هر روز با شرط خاص برای 'default'
   $appointments = DB::table('counseling_appointments')
    ->select(DB::raw('appointment_date, COUNT(*) as appointment_count'))
    ->where('doctor_id', $doctorId)
    ->where('status', 'scheduled')
    ->whereNull('deleted_at') // فیلتر برای نوبت‌های فعال
    ->when($selectedClinicId === 'default', function ($query) use ($doctorId) {
     // در صورت 'default' فقط نوبت‌های بدون کلینیک (clinic_id = NULL) مرتبط با پزشک
     $query->whereNull('clinic_id')->where('doctor_id', $doctorId);
    })
    ->when($selectedClinicId && $selectedClinicId !== 'default', function ($query) use ($selectedClinicId) {
     // در صورت ارسال کلینیک خاص
     $query->where('clinic_id', $selectedClinicId);
    })
    ->groupBy('appointment_date')
    ->get();

   // قالب‌بندی داده‌ها
   $data = $appointments->map(function ($item) {
    return [
     'appointment_date' => $item->appointment_date,
     'appointment_count' => $item->appointment_count,
    ];
   });

   return response()->json([
    'status' => true,
    'data' => $data,
   ]);
  } catch (\Exception $e) {
   return response()->json([
    'status' => false,
    'message' => 'خطا در دریافت داده‌ها',
    'error' => $e->getMessage(), // نمایش پیام خطا برای دیباگ بهتر
   ], 500);
  }
 }
 public function getHolidayDates(Request $request)
 {
  // دریافت شناسه پزشک یا منشی
  $doctorId = Auth::guard('doctor')->id() ?? Auth::guard('secretary')->id();
  $selectedClinicId = $request->input('selectedClinicId'); // کلینیک انتخابی

  // جستجوی تعطیلی‌های پزشک با شرط‌های لازم
  $holidayQuery = CounselingHoliday::where('doctor_id', $doctorId)
   ->when($selectedClinicId === 'default', function ($query) use ($doctorId) {
    // در صورت 'default' فقط تعطیلی‌های بدون کلینیک (clinic_id = NULL) بازگردانده شود
    $query->whereNull('clinic_id')->where('doctor_id', $doctorId);
   })
   ->when($selectedClinicId && $selectedClinicId !== 'default', function ($query) use ($selectedClinicId) {
    // در صورت ارسال کلینیک خاص
    $query->where('clinic_id', $selectedClinicId);
   });

  $holidayRecord = $holidayQuery->first();
  $holidays = [];

  // اگر رکورد تعطیلی وجود داشت و تاریخ‌های تعطیلی خالی نبودند
  if ($holidayRecord && !empty($holidayRecord->holiday_dates)) {
   $decodedHolidays = json_decode($holidayRecord->holiday_dates, true);
   $holidays = is_array($decodedHolidays) ? $decodedHolidays : [];
  }

  return response()->json([
   'status' => true,
   'holidays' => $holidays,
  ]);
 }
 public function toggleHolidayStatus(Request $request)
 {
  $validated = $request->validate([
   'date' => 'required|date',
   'selectedClinicId' => 'nullable|string',
  ]);

  $doctorId = Auth::guard('doctor')->id() ?? Auth::guard('secretary')->id();
  $selectedClinicId = $request->input('selectedClinicId');

  // بازیابی یا ایجاد رکورد تعطیلات با شرط کلینیک
  $holidayRecordQuery = CounselingHoliday::where('doctor_id', $doctorId);

  if ($selectedClinicId === 'default') {
   $holidayRecordQuery->whereNull('clinic_id');
  } elseif ($selectedClinicId && $selectedClinicId !== 'default') {
   $holidayRecordQuery->where('clinic_id', $selectedClinicId);
  }

  $holidayRecord = $holidayRecordQuery->firstOrCreate([
   'doctor_id' => $doctorId,
   'clinic_id' => ($selectedClinicId !== 'default' ? $selectedClinicId : null),
  ], [
   'holiday_dates' => json_encode([])
  ]);

  // بررسی و تبدیل JSON به آرایه
  $holidayDates = json_decode($holidayRecord->holiday_dates, true);
  if (!is_array($holidayDates)) {
   $holidayDates = [];
  }

  // بررسی وجود تاریخ و تغییر وضعیت
  if (in_array($validated['date'], $holidayDates)) {
   // حذف تاریخ از لیست تعطیلات
   $holidayDates = array_diff($holidayDates, [$validated['date']]);
   $message = 'این تاریخ از حالت تعطیلی خارج شد.';
   $isHoliday = false;
  } else {
   // اضافه کردن تاریخ به لیست تعطیلات
   $holidayDates[] = $validated['date'];
   $message = 'این تاریخ تعطیل شد.';
   $isHoliday = true;

   // حذف SpecialDailySchedule مرتبط با کلینیک
   $specialDayQuery = CounselingDailySchedule::where('date', $validated['date']);

   if ($selectedClinicId === 'default') {
    $specialDayQuery->whereNull('clinic_id');
   } elseif ($selectedClinicId && $selectedClinicId !== 'default') {
    $specialDayQuery->where('clinic_id', $selectedClinicId);
   }

   $specialDayQuery->delete();
  }

  // به‌روزرسانی رکورد تعطیلات
  $holidayRecord->update([
   'holiday_dates' => json_encode(array_values($holidayDates))
  ]);

  return response()->json([
   'status' => true,
   'is_holiday' => $isHoliday,
   'message' => $message,
   'holiday_dates' => $holidayDates,
  ]);
 }
 public function getNextAvailableDate(Request $request)
 {
  // دریافت شناسه پزشک یا منشی
  $doctorId = Auth::guard('doctor')->id() ?? Auth::guard('secretary')->id();
  $selectedClinicId = $request->input('selectedClinicId'); // کلینیک انتخابی

  // دریافت تعطیلی‌های پزشک با توجه به کلینیک
  $holidaysQuery = CounselingHoliday::where('doctor_id', $doctorId)
   ->when($selectedClinicId === 'default', function ($query) use ($doctorId) {
    // در صورت 'default' فقط تعطیلی‌های بدون کلینیک (clinic_id = NULL)
    $query->whereNull('clinic_id');
   })
   ->when($selectedClinicId && $selectedClinicId !== 'default', function ($query) use ($selectedClinicId) {
    // اگر کلینیک خاص ارسال شود
    $query->where('clinic_id', $selectedClinicId);
   });

  $holidays = $holidaysQuery->first();
  $holidayDates = json_decode($holidays->holiday_dates ?? '[]', true);

  // تعداد روزهای قابل بررسی برای نوبت خالی
  $today = Carbon::now()->startOfDay();
  $daysToCheck = DoctorCounselingConfig::where('doctor_id', $doctorId)->value('calendar_days') ?? 30;

  // تولید لیست تاریخ‌ها برای بررسی
  $datesToCheck = collect();
  for ($i = 1; $i <= $daysToCheck; $i++) {
   $date = $today->copy()->addDays($i)->format('Y-m-d');
   $datesToCheck->push($date);
  }

  // پیدا کردن اولین تاریخ خالی
  $nextAvailableDate = $datesToCheck->first(function ($date) use ($doctorId, $holidayDates, $selectedClinicId) {
   // بررسی عدم وجود در لیست تعطیلی‌ها
   if (in_array($date, $holidayDates)) {
    return false;
   }

   // بررسی عدم وجود نوبت در تاریخ مورد نظر
   $appointmentQuery = CounselingAppointment::where('doctor_id', $doctorId)
    ->where('appointment_date', $date)
    ->when($selectedClinicId === 'default', function ($query) {
     // فقط نوبت‌های بدون کلینیک (clinic_id = NULL) بازگردانده شود
     $query->whereNull('clinic_id');
    })
    ->when($selectedClinicId && $selectedClinicId !== 'default', function ($query) use ($selectedClinicId) {
     // نوبت‌های کلینیک مشخص‌شده بازگردانده شود
     $query->where('clinic_id', $selectedClinicId);
    });

   return !$appointmentQuery->exists();
  });

  return response()->json([
   'status' => $nextAvailableDate ? true : false,
   'date' => $nextAvailableDate ?? 'هیچ نوبت خالی یافت نشد.'
  ]);
 }
 public function updateFirstAvailableAppointment(Request $request)
 {
  // اعتبارسنجی ورودی
  $validated = $request->validate([
   'old_date' => 'required|date', // تاریخ قبلی نوبت
   'new_date' => 'required|date', // تاریخ جدید که باید جایگزین شود
   'selectedClinicId' => 'nullable|string', // اضافه کردن فیلتر selectedClinicId
  ]);

  $doctorId = Auth::guard('doctor')->id() ?? Auth::guard('secretary')->id();
  $selectedClinicId = $request->input('selectedClinicId'); // دریافت selectedClinicId از درخواست

  try {
   // پیدا کردن تمام نوبت‌های اولین تاریخ ثبت‌شده با فیلتر کلینیک
   $appointmentsQuery = CounselingAppointment::where('doctor_id', $doctorId)
    ->where('appointment_date', $validated['old_date'])
    ->when($selectedClinicId === 'default', function ($query) {
     // اگر selectedClinicId برابر با 'default' باشد، فقط نوبت‌های بدون کلینیک را در نظر بگیرد
     $query->whereNull('clinic_id');
    })
    ->when($selectedClinicId && $selectedClinicId !== 'default', function ($query) use ($selectedClinicId) {
     // در غیر این صورت، نوبت‌های مربوط به کلینیک مشخص‌شده را بررسی کند
     $query->where('clinic_id', $selectedClinicId);
    });

   $appointments = $appointmentsQuery->get();

   if ($appointments->isEmpty()) {
    return response()->json([
     'status' => false,
     'message' => 'هیچ نوبتی برای بروزرسانی یافت نشد.'
    ], 404);
   }

   // بررسی ساعات کاری پزشک برای تاریخ جدید
   $selectedDate = Carbon::parse($validated['new_date']); // تبدیل تاریخ جدید به میلادی
   $dayOfWeek = strtolower($selectedDate->format('l'));
   // بررسی ساعات کاری پزشک برای تاریخ جدید
   $workHours = DoctorCounselingWorkSchedule::where('doctor_id', $doctorId)
    ->where('day', $dayOfWeek)
    ->when($selectedClinicId === 'default', function ($query) {
     $query->whereNull('clinic_id');
    })
    ->when($selectedClinicId && $selectedClinicId !== 'default', function ($query) use ($selectedClinicId) {
     $query->where('clinic_id', $selectedClinicId);
    })
    ->first();

   // دیباگ برای بررسی کوئری ساعات کاری
   if (!$workHours) {
    return response()->json([
     'status' => false,
     'message' => 'ساعات کاری پزشک برای تاریخ جدید یافت نشد.',
     'debug' => [
      'doctor_id' => $doctorId,
      'clinic_id' => $selectedClinicId,
      'day' => $dayOfWeek,
     ]
    ], 400);
   }


   // لیست شماره‌های موبایل کاربران
   $recipients = [];

   foreach ($appointments as $appointment) {
    // ذخیره تاریخ قبلی برای پیامک
    $oldDate = $appointment->appointment_date;

    // به‌روزرسانی تاریخ نوبت
    $appointment->appointment_date = $validated['new_date'];
    $appointment->save();

    // اضافه کردن شماره موبایل به لیست دریافت‌کنندگان پیامک
    if ($appointment->patient && $appointment->patient->mobile) {
     $recipients[] = $appointment->patient->mobile;
    }
   }

   // تبدیل تاریخ‌ها به فرمت شمسی
   $oldDateJalali = \Morilog\Jalali\Jalalian::fromDateTime($validated['old_date'])->format('Y/m/d');
   $newDateJalali = \Morilog\Jalali\Jalalian::fromDateTime($validated['new_date'])->format('Y/m/d');

   // ارسال پیامک به همه کاربران
   if (!empty($recipients)) {
    $messageContent = "کاربر گرامی، نوبت شما از تاریخ {$oldDateJalali} به تاریخ {$newDateJalali} تغییر یافت.";

    foreach ($recipients as $recipient) {
     $user = User::where('mobile', $recipient)->first();
     $userFullName = $user ? $user->first_name . " " . $user->last_name : 'کاربر گرامی';

     $messagesService = new MessageService(
      SmsService::create(100252, $recipient, [$userFullName, $oldDateJalali, $newDateJalali, 'به نوبه'])
     );
     $messagesService->send();
    }
   }

   return response()->json([
    'status' => true,
    'message' => 'نوبت‌ها با موفقیت بروزرسانی شدند و پیامک ارسال گردید.',
    'total_recipients' => count($recipients),
   ]);
  } catch (\Exception $e) {
   return response()->json([
    'status' => false,
    'message' => 'خطا در بروزرسانی نوبت‌ها.',
    'error' => $e->getMessage()
   ], 500);
  }
 }
 public function getDefaultSchedule(Request $request)
 {
  $doctorId = Auth::guard('doctor')->user()->id;
  $date = $request->date;
  $selectedDate = Carbon::parse($request->date); // تاریخ دریافتی در فرمت میلادی
  $selectedClinicId = $request->input('selectedClinicId');
  $dayOfWeek = strtolower($selectedDate->format('l')); // دریافت نام روز (مثلاً saturday, sunday, ...)

  // Check for special schedule
  $specialScheduleQuery = CounselingDailySchedule::where('date', $date);
  if ($selectedClinicId && $selectedClinicId !== 'default') {
   $specialScheduleQuery->where('clinic_id', $selectedClinicId);
  }
  $specialSchedule = $specialScheduleQuery->first();

  // بررسی وجود ساعات کاری برای تاریخ مشخص در جدول ویژه
  if ($specialSchedule) {
   return response()->json([
    'status' => true,
    'work_hours' => json_decode($specialSchedule->work_hours, true)
   ]);
  }

  // دریافت ساعات کاری دکتر برای این روز خاص
  $workScheduleQuery = DoctorCounselingWorkSchedule::where('doctor_id', $doctorId)
   ->where('day', $dayOfWeek);
  if ($selectedClinicId && $selectedClinicId !== 'default') {
   $workScheduleQuery->where('clinic_id', $selectedClinicId);
  }
  $workSchedule = $workScheduleQuery->first();

  if ($workSchedule) {
   return response()->json([
    'status' => true,
    'work_hours' => json_decode($workSchedule->work_hours, true) ?? []
   ]);
  }

  return response()->json([
   'status' => false,
   'message' => 'هیچ ساعات کاری برای این روز یافت نشد.'
  ]);
 }
 public function cancelAppointments(Request $request)
 {
  // اعتبارسنجی ورودی‌ها
  $validatedData = $request->validate([
   'date' => 'required|date',
   'selectedClinicId' => 'nullable|string',
  ]);

  $selectedClinicId = $request->input('selectedClinicId');

  // دریافت نوبت‌ها با اعمال فیلتر کلینیک
  $appointmentsQuery = CounselingAppointment::where('appointment_date', $validatedData['date'])
   ->when($selectedClinicId === 'default', function ($query) {
    // اگر selectedClinicId برابر با 'default' باشد، فقط نوبت‌های بدون کلینیک را در نظر بگیرد
    $query->whereNull('clinic_id');
   })
   ->when($selectedClinicId && $selectedClinicId !== 'default', function ($query) use ($selectedClinicId) {
    // اگر selectedClinicId مشخص شده باشد و برابر 'default' نباشد
    $query->where('clinic_id', $selectedClinicId);
   });

  $appointments = $appointmentsQuery->get();

  // لغو نوبت‌ها (حذف نرم‌افزاری)
  foreach ($appointments as $appointment) {
   $appointment->status = 'cancelled';
   $appointment->deleted_at = now();
   $appointment->save();
  }

  return response()->json([
   'status' => true,
   'message' => 'نوبت‌ها با موفقیت لغو شدند.',
   'total_cancelled' => $appointments->count(),
  ]);
 }
}