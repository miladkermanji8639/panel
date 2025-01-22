<?php

namespace App\Http\Controllers\Dr\Panel\Turn\Schedule\ManualNobat;

use Log;
use App\Models\User;
use Illuminate\Http\Request;
use Morilog\Jalali\CalendarUtils;
use Illuminate\Support\Facades\DB;
use App\Models\Dr\ManualAppointment;
use App\Models\Dr\ManualAppointmentSetting;

class ManualNobatController
{
  /**
   * Display a listing of the resource.
   */
  public function index(Request $request)
  {
    try {
      $appointments = ManualAppointment::with('user')->get();

      // بررسی نوع درخواست
      if ($request->ajax()) {
        return response()->json([
          'success' => true,
          'data' => $appointments,
        ]);
      }

      return view('dr.panel.turn.schedule.manual_nobat.index', compact('appointments'));
    } catch (\Exception $e) {
      Log::error('Error in fetching appointments: ' . $e->getMessage());
      if ($request->ajax()) {
        return response()->json([
          'success' => false,
          'message' => 'خطا در بازیابی نوبت‌ها!',
        ], 500);
      }
      return abort(500, 'خطا در بازیابی اطلاعات!');
    }
  }
  public function showSettings()
  {
    $settings = ManualAppointmentSetting::where('doctor_id', auth('doctor')->id())->first();

    return view('dr.panel.turn.schedule.manual_nobat.manual-nobat-setting', compact('settings'));
  }
  public function saveSettings(Request $request)
  {
    
    $request->validate([
      'status' => 'required|boolean',
      'duration_send_link' => 'required|integer|min:1',
      'duration_confirm_link' => 'required|integer|min:1',
    ]);

    try {
      ManualAppointmentSetting::updateOrCreate(
        ['doctor_id' => auth('doctor')->id()],
        [
          'is_active' => $request->status,
          'duration_send_link' => $request->duration_send_link,
          'duration_confirm_link' => $request->duration_confirm_link,
        ]
      );

      return response()->json(['success' => true, 'message' => 'تنظیمات با موفقیت ذخیره شد.']);
    } catch (\Exception $e) {
      return response()->json(['success' => false, 'message' => 'خطا در ذخیره تنظیمات.'], 500);
    }
  }



  public function searchUsers(Request $request)
  {
    try {
      $query = $request->get('query');

      // جستجو در جدول کاربران بر اساس نام، نام خانوادگی، شماره موبایل و کد ملی
      $users = User::where('first_name', 'LIKE', "%{$query}%")
        ->orWhere('last_name', 'LIKE', "%{$query}%")
        ->orWhere('mobile', 'LIKE', "%{$query}%")
        ->orWhere('national_code', 'LIKE', "%{$query}%")
        ->get();

      return response()->json($users);
    } catch (\Exception $e) {
      // ثبت خطا در لاگ لاراول
      Log::error('Error in searchUsers: ' . $e->getMessage());
      return response()->json(['error' => 'Internal Server Error'], 500);
    }
  }


  /**
   * Show the form for creating a new resource.
   */
  public function create()
  {
    //
  }

  /**
   * Store a newly created resource in storage.
   */
  public function store(Request $request)
  {
    $validatedData = $request->validate([
      'user_id' => 'required|exists:users,id',
      'doctor_id' => 'required|exists:doctors,id',
      'appointment_date' => 'required|date',
      'appointment_time' => 'required|date_format:H:i',
      'description' => 'nullable|string|max:1000',
    ]);

    try {
      $gregorianDate = CalendarUtils::createDatetimeFromFormat('Y/m/d', $request->appointment_date)->format('Y-m-d');
      $validatedData['appointment_date'] = $gregorianDate;
      // بررسی نوبت تکراری
      $existingAppointment = ManualAppointment::where('user_id', $validatedData['user_id'])
        ->first();
        Log::info($existingAppointment);

      if ($existingAppointment) {
        return response()->json(['success' => false, 'message' => 'نوبت تکراری است.'], 400);
      }

      ManualAppointment::create($validatedData);

      return response()->json(['success' => true, 'message' => 'نوبت با موفقیت ثبت شد.']);
    } catch (\Exception $e) {
      Log::error('Error in ManualNobatController@store: ' . $e->getMessage());
      return response()->json(['success' => false, 'message' => 'خطا در ثبت نوبت.'], 500);
    }
  }


  public function storeWithUser(Request $request)
  {
    $validatedData = $request->validate([
      'first_name' => 'required|string|max:255',
      'last_name' => 'required|string|max:255',
      'mobile' => 'required|digits:11|unique:users,mobile',
      'national_code' => 'required|digits:10|unique:users,national_code',
      'appointment_date' => 'required|date',
      'appointment_time' => 'required|date_format:H:i',
      'description' => 'nullable|string|max:1000',
    ]);
    try {
      $gregorianDate = CalendarUtils::createDatetimeFromFormat('Y/m/d', $request->appointment_date)->format('Y-m-d');
      $validatedData['appointment_date'] = $gregorianDate;

      DB::beginTransaction();
      // ایجاد کاربر
      $user = User::create([
        'first_name' => $validatedData['first_name'],
        'last_name' => $validatedData['last_name'],
        'mobile' => $validatedData['mobile'],
        'national_code' => $validatedData['national_code'],
      ]);
      // ایجاد نوبت
      $appointment = ManualAppointment::create([
        'user_id' => $user->id,
        'doctor_id' => auth('doctor')->id(),
        'appointment_date' => $validatedData['appointment_date'],
        'appointment_time' => $validatedData['appointment_time'],
        'description' => $validatedData['description'],
      ]);

      DB::commit();

      // بازگرداندن داده‌های نوبت همراه با کاربر
      $appointment->load('user'); // بارگذاری اطلاعات مرتبط با کاربر

      return response()->json(['data' => $appointment], 201);
    } catch (\Exception $e) {
      DB::rollBack();
      Log::error('Error in storeWithUser: ' . $e->getMessage());
      return response()->json(['error' => 'خطا در ذخیره اطلاعات!'], 500);
    }
  }





  /**
   * Display the specified resource.
   */
  public function show(string $id)
  {
    //
  }

  /**
   * Show the form for editing the specified resource.
   */
  public function edit($id)
  {
    try {
      $appointment = ManualAppointment::with('user')->findOrFail($id);
      return response()->json(['success' => true, 'data' => $appointment]);
    } catch (\Exception $e) {
      Log::error('Error in edit: ' . $e->getMessage());
      return response()->json(['success' => false, 'message' => 'خطا در دریافت اطلاعات!'], 500);
    }
  }

  public function update(Request $request, $id)
  {
    $validatedData = $request->validate([
      'first_name' => 'required|string|max:255',
      'last_name' => 'required|string|max:255',
      'mobile' => 'required|digits:11',
      'national_code' => 'required|digits:10',
      'appointment_date' => 'required|date',
      'appointment_time' => 'required|date_format:H:i',
      'description' => 'nullable|string|max:1000',
    ]);

    try {
      $appointment = ManualAppointment::findOrFail($id);

      // به‌روزرسانی اطلاعات کاربر مرتبط
      $appointment->user->update([
        'first_name' => $validatedData['first_name'],
        'last_name' => $validatedData['last_name'],
        'mobile' => $validatedData['mobile'],
        'national_code' => $validatedData['national_code'],
      ]);

      // به‌روزرسانی اطلاعات نوبت
      $appointment->update([
        'appointment_date' => CalendarUtils::createDatetimeFromFormat('Y/m/d', $request->appointment_date)->format('Y-m-d'),
        'appointment_time' => $validatedData['appointment_time'],
        'description' => $validatedData['description'],
      ]);

      return response()->json(['success' => true, 'message' => 'نوبت با موفقیت ویرایش شد.']);
    } catch (\Exception $e) {
      Log::error('Error in update: ' . $e->getMessage());
      return response()->json(['success' => false, 'message' => 'خطا در ویرایش نوبت.'], 500);
    }
  }


  /**
   * Remove the specified resource from storage.
   */
  public function destroy($id)
  {
    Log::info($id);
    try {
      $appointment = ManualAppointment::findOrFail($id);
      $appointment->delete();

      return response()->json(['success' => true, 'message' => 'نوبت با موفقیت حذف شد!']);
    } catch (\Exception $e) {
      Log::error('Error in destroy: ' . $e->getMessage());
      return response()->json(['success' => false, 'message' => 'خطا در حذف نوبت!'], 500);
    }
  }

}
