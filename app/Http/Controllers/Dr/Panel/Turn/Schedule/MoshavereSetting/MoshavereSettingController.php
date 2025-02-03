<?php
namespace App\Http\Controllers\Dr\Panel\Turn\Schedule\MoshavereSetting;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Models\Dr\Appointment;
use App\Models\Dr\DoctorHoliday;
use App\Models\Dr\DoctorCounselingSlot;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use App\Models\Dr\DoctorCounselingConfig;
use App\Models\Dr\DoctorCounselingWorkSchedule;
class MoshavereSettingController
{
  /**
   * Display a listing of the resource.
   */
  public function index()
  {
    $doctorId = Auth::guard('doctor')->id() ?? Auth::guard('secretary')->user()->doctor_id;
    // بررسی یا ایجاد تنظیمات مشاوره آنلاین
    $appointmentConfig = DoctorCounselingConfig::firstOrCreate(
      ['doctor_id' => $doctorId],
      [
        'auto_scheduling' => true,
        'calendar_days' => 30,
        'online_consultation' => false,
        'holiday_availability' => false,
      ]
    );
    return view('dr.panel.turn.schedule.moshavere_setting.index', [
      'appointmentConfig' => $appointmentConfig,
    ]);
  }
  /**
   * Show the form for creating a new resource.
   */

  public function workhours()
  {
    $doctorId = Auth::guard('doctor')->id() ?? Auth::guard('secretary')->id();
    $appointmentConfig = DoctorCounselingConfig::firstOrCreate(
      ['doctor_id' => $doctorId],
      [
        'auto_scheduling' => true,
        'online_consultation' => false,
        'holiday_availability' => false
      ]
    );
    $workSchedules = DoctorCounselingWorkSchedule::where('doctor_id', $doctorId)->get();
    return view("dr.panel.turn.schedule.scheduleSetting.workhours", [
      'appointmentConfig' => $appointmentConfig,
      'workSchedules' => $workSchedules
    ]);
  }
  public function copyWorkHours(Request $request)
  {
    $override = filter_var($request->input('override', false), FILTER_VALIDATE_BOOLEAN);
    $validated = $request->validate([
      'source_day' => 'required|string',
      'target_days' => 'required|array|min:1',
      'override' => 'nullable|in:0,1,true,false'
    ]);
    $doctor = Auth::guard('doctor')->user() ?? Auth::guard('secretary')->user();
    DB::beginTransaction();
    try {
      // دریافت ساعات کاری روز مبدأ
      $sourceWorkSchedule = DoctorCounselingWorkSchedule::where('doctor_id', $doctor->id)
        ->where('day', $validated['source_day'])
        ->first();
      if (!$sourceWorkSchedule || empty($sourceWorkSchedule->work_hours)) {
        return response()->json([
          'message' => 'روز مبدأ یافت نشد یا فاقد ساعات کاری است.',
          'status' => false
        ], 404);
      }
      // تبدیل ساعات کاری روز مبدأ به آرایه
      $sourceWorkHours = json_decode($sourceWorkSchedule->work_hours, true) ?? [];
      foreach ($validated['target_days'] as $targetDay) {
        $targetWorkSchedule = DoctorCounselingWorkSchedule::firstOrCreate(
          [
            'doctor_id' => $doctor->id,
            'day' => $targetDay,
          ],
          [
            'is_working' => true,
            'work_hours' => json_encode([])
          ]
        );
        // اگر حالت override فعال باشد، ساعات قبلی حذف می‌شوند
        if ($override) {
          $targetWorkSchedule->work_hours = json_encode($sourceWorkHours);
        } else {
          // بررسی تداخل زمانی با ساعات کاری فعلی روز مقصد
          $existingWorkHours = json_decode($targetWorkSchedule->work_hours, true) ?? [];
          foreach ($sourceWorkHours as $sourceSlot) {
            foreach ($existingWorkHours as $existingSlot) {
              $sourceStart = Carbon::createFromFormat('H:i', $sourceSlot['start']);
              $sourceEnd = Carbon::createFromFormat('H:i', $sourceSlot['end']);
              $existingStart = Carbon::createFromFormat('H:i', $existingSlot['start']);
              $existingEnd = Carbon::createFromFormat('H:i', $existingSlot['end']);
              if (
                ($sourceStart >= $existingStart && $sourceStart < $existingEnd) ||
                ($sourceEnd > $existingStart && $sourceEnd <= $existingEnd) ||
                ($sourceStart <= $existingStart && $sourceEnd >= $existingEnd)
              ) {
                return response()->json([
                  'message' => 'بازه زمانی ' . $sourceStart->format('H:i') . ' تا ' . $sourceEnd->format('H:i') . ' با بازه‌های موجود تداخل دارد.',
                  'status' => false,
                  'day' => $targetDay
                ], 400);
              }
            }
          }
          // اضافه کردن بازه‌های جدید بدون حذف قبلی‌ها
          $mergedWorkHours = array_merge($existingWorkHours, $sourceWorkHours);
          $targetWorkSchedule->work_hours = json_encode($mergedWorkHours);
        }
        $targetWorkSchedule->save();
      }
      DB::commit();
      return response()->json([
        'message' => 'ساعات کاری با موفقیت کپی شد',
        'status' => true,
        'target_days' => $validated['target_days'],
        'workSchedules' => DoctorCounselingWorkSchedule::where('doctor_id', $doctor->id)
          ->whereIn('day', $validated['target_days']) // ارتباط با ساعات کاری‌ها
          ->get()
      ]);
    } catch (\Exception $e) {
      DB::rollBack();
      return response()->json([
        'message' => 'خطا در کپی ساعات کاری. لطفاً مجدداً تلاش کنید.',
        'status' => false
      ], 500);
    }
  }
  public function copySingleSlot(Request $request)
  {
    $validated = $request->validate([
      'source_day' => 'required|in:saturday,sunday,monday,tuesday,wednesday,thursday,friday',
      'target_days' => 'required|array|min:1',
      'start_time' => 'required|date_format:H:i',
      'end_time' => 'required|date_format:H:i|after:start_time',
      'override' => 'nullable|in:0,1,true,false'
    ]);
    $override = filter_var($request->input('override', false), FILTER_VALIDATE_BOOLEAN);
    $doctor = Auth::guard('doctor')->user() ?? Auth::guard('secretary')->user();
    DB::beginTransaction();
    try {
      $sourceWorkSchedule = DoctorCounselingWorkSchedule::where('doctor_id', $doctor->id)
        ->where('day', $validated['source_day'])
        ->first();
      if (!$sourceWorkSchedule || empty($sourceWorkSchedule->work_hours)) {
        return response()->json([
          'message' => 'روز مبدأ یافت نشد یا فاقد ساعات کاری است.',
          'status' => false
        ], 404);
      }
      $sourceWorkHours = json_decode($sourceWorkSchedule->work_hours, true) ?? [];
      // یافتن بازه موردنظر برای کپی
      $slotToCopy = collect($sourceWorkHours)->first(function ($slot) use ($validated) {
        return $slot['start'] === $validated['start_time'] && $slot['end'] === $validated['end_time'];
      });
      if (!$slotToCopy) {
        return response()->json([
          'message' => 'ساعات کاری مورد نظر برای کپی یافت نشد.',
          'status' => false
        ], 404);
      }
      foreach ($validated['target_days'] as $targetDay) {
        $targetWorkSchedule = DoctorCounselingWorkSchedule::firstOrCreate(
          [
            'doctor_id' => $doctor->id,
            'day' => $targetDay,
          ],
          [
            'is_working' => true,
            'work_hours' => json_encode([])
          ]
        );
        $existingWorkHours = json_decode($targetWorkSchedule->work_hours, true) ?? [];
        if ($override) {
          // حذف بازه‌های متداخل
          $existingWorkHours = array_filter($existingWorkHours, function ($existingSlot) use ($validated) {
            return !(
              ($existingSlot['start'] == $validated['start_time'] && $existingSlot['end'] == $validated['end_time'])
            );
          });
        } else {
          // بررسی تداخل زمانی
          foreach ($existingWorkHours as $existingSlot) {
            $existingStart = Carbon::createFromFormat('H:i', $existingSlot['start']);
            $existingEnd = Carbon::createFromFormat('H:i', $existingSlot['end']);
            $newStart = Carbon::createFromFormat('H:i', $slotToCopy['start']);
            $newEnd = Carbon::createFromFormat('H:i', $slotToCopy['end']);
            if (
              ($newStart >= $existingStart && $newStart < $existingEnd) ||
              ($newEnd > $existingStart && $newEnd <= $existingEnd) ||
              ($newStart <= $existingStart && $newEnd >= $existingEnd)
            ) {
              return response()->json([
                'message' => 'بازه زمانی ' . $newStart->format('H:i') . ' تا ' . $newEnd->format('H:i') . ' با بازه‌های موجود تداخل دارد.',
                'status' => false,
                'day' => $targetDay
              ], 400);
            }
          }
        }
        // اضافه کردن بازه جدید
        $existingWorkHours[] = $slotToCopy;
        $targetWorkSchedule->work_hours = json_encode(array_values($existingWorkHours));
        $targetWorkSchedule->save();
      }
      DB::commit();
      return response()->json([
        'message' => 'ساعات کاری با موفقیت کپی شد',
        'status' => true,
        'target_days' => $validated['target_days']
      ]);
    } catch (\Exception $e) {
      DB::rollBack();
      return response()->json([
        'message' => 'خطا در کپی ساعات کاری',
        'status' => false
      ], 500);
    }
  }
  // تابع کمکی برای تبدیل روز به فارسی
  private function getDayNameInPersian($day)
  {
    $days = [
      'saturday' => 'شنبه',
      'sunday' => 'یکشنبه',
      'monday' => 'دوشنبه',
      'tuesday' => 'سه‌شنبه',
      'wednesday' => 'چهارشنبه',
      'thursday' => 'پنج‌شنبه',
      'friday' => 'جمعه'
    ];
    return $days[$day] ?? $day;
  }
  public function checkDaySlots(Request $request)
  {
    $validated = $request->validate([
      'day' => 'required|in:saturday,sunday,monday,tuesday,wednesday,thursday,friday'
    ]);
    $doctor = Auth::guard('doctor')->user() ?? Auth::guard('secretary')->user();
    $workSchedule = DoctorCounselingWorkSchedule::where('doctor_id', $doctor->id)
      ->where('day', $validated['day'])
      ->first();
    // بررسی اینکه آیا ساعات کاری به صورت JSON ذخیره شده است و مقدار دارد
    $hasSlots = $workSchedule && !empty(json_decode($workSchedule->work_hours, true));
    return response()->json(['hasSlots' => $hasSlots]);
  }
  public function saveTimeSlot(Request $request)
  {
    $validated = $request->validate([
      'day' => 'required|in:saturday,sunday,monday,tuesday,wednesday,thursday,friday',
      'start_time' => 'required|date_format:H:i',
      'end_time' => 'required|date_format:H:i|after:start_time',
      'max_appointments' => 'required|integer|min:1'
    ]);
    $doctor = Auth::guard('doctor')->user() ?? Auth::guard('secretary')->user();
    try {
      // پیدا کردن رکورد ساعات کاری پزشک در روز مورد نظر
      $workSchedule = DoctorCounselingWorkSchedule::firstOrCreate(
        [
          'doctor_id' => $doctor->id,
          'day' => $validated['day'],
        ],
        [
          'is_working' => true,
          'work_hours' => json_encode([]),
        ]
      );
      // دریافت `work_hours` فعلی
      $existingWorkHours = json_decode($workSchedule->work_hours, true) ?? [];
      // بررسی تداخل زمانی با ساعات موجود
      foreach ($existingWorkHours as $hour) {
        $existingStart = Carbon::createFromFormat('H:i', $hour['start']);
        $existingEnd = Carbon::createFromFormat('H:i', $hour['end']);
        $newStart = Carbon::createFromFormat('H:i', $validated['start_time']);
        $newEnd = Carbon::createFromFormat('H:i', $validated['end_time']);
        if (
          ($newStart >= $existingStart && $newStart < $existingEnd) ||
          ($newEnd > $existingStart && $newEnd <= $existingEnd) ||
          ($newStart <= $existingStart && $newEnd >= $existingEnd)
        ) {
          return response()->json([
            'message' => 'این بازه زمانی با بازه‌های موجود تداخل دارد.',
            'status' => false
          ], 400);
        }
      }
      // اضافه کردن ساعات کاری جدید به `work_hours`
      $newSlot = [
        'start' => $validated['start_time'],
        'end' => $validated['end_time'],
        'max_appointments' => $validated['max_appointments'],
      ];
      $existingWorkHours[] = $newSlot;
      // ذخیره تغییرات
      $workSchedule->update([
        'work_hours' => json_encode($existingWorkHours),
      ]);
      return response()->json([
        'message' => 'نوبت با موفقیت ذخیره شد',
        'status' => true,
        'work_hours' => $existingWorkHours
      ]);
    } catch (\Exception $e) {
      return response()->json([
        'message' => 'خطا در ذخیره‌سازی نوبت',
        'status' => false
      ], 500);
    }
  }
  public function updateWorkDayStatus(Request $request)
  {
    $validated = $request->validate([
      'day' => 'required|in:saturday,sunday,monday,tuesday,wednesday,thursday,friday',
      'is_working' => 'required|in:0,1,true,false'
    ]);
    try {
      $doctor = Auth::guard('doctor')->user() ?? Auth::guard('secretary')->user();
      $isWorking = filter_var($validated['is_working'], FILTER_VALIDATE_BOOLEAN);
      $workSchedule = DoctorCounselingWorkSchedule::updateOrCreate(
        [
          'doctor_id' => $doctor->id,
          'day' => $validated['day']
        ],
        [
          'is_working' => $isWorking
        ]
      );
      return response()->json([
        'message' => $isWorking ? 'روز کاری با موفقیت فعال شد' : 'روز کاری با موفقیت غیرفعال شد',
        'status' => true,
        'data' => $workSchedule
      ], 200);
    } catch (\Exception $e) {
      return response()->json([
        'message' => 'خطا در بروزرسانی وضعیت روز کاری',
        'status' => false
      ], 500);
    }
  }
  public function updateAutoScheduling(Request $request)
  {
    $validated = $request->validate([
      'auto_scheduling' => [
        'required',
        'in:0,1,true,false', // Explicitly allow these values
      ],
    ]);
    // Convert to strict boolean
    $autoScheduling = filter_var($validated['auto_scheduling'], FILTER_VALIDATE_BOOLEAN);
    try {
      $doctor = Auth::guard('doctor')->user() ?? Auth::guard('secretary')->user();
      $appointmentConfig = DoctorCounselingConfig::updateOrCreate(
        ['doctor_id' => $doctor->id],
        ['auto_scheduling' => $autoScheduling]
      );
      return response()->json([
        'message' => $autoScheduling
          ? 'نوبت‌دهی خودکار فعال شد'
          : 'نوبت‌دهی خودکار غیرفعال شد',
        'status' => true,
        'data' => [
          'auto_scheduling' => $appointmentConfig->auto_scheduling
        ]
      ]);
    } catch (\Exception $e) {
      return response()->json([
        'message' => 'خطا در به‌روزرسانی تنظیمات',
        'status' => false
      ], 500);
    }
  }
  public function saveAppointmentSettings(Request $request)
  {
    $validated = $request->validate([
      'day' => 'required|in:saturday,sunday,monday,tuesday,wednesday,thursday,friday',
      'start_time' => 'required|date_format:H:i',
      'end_time' => 'required|date_format:H:i|after:start_time',
      'max_appointments' => 'nullable|integer|min:1',
      'selected_days' => 'required|in:saturday,sunday,monday,tuesday,wednesday,thursday,friday'
    ]);
    try {
      $doctor = Auth::guard('doctor')->user() ?? Auth::guard('secretary')->user();
      // تبدیل selected_days به آرایه
      $selectedDays = is_array($request->input('selected_days'))
        ? $request->input('selected_days')
        : explode(',', $request->input('selected_days'));
      $results = [];
      foreach ($selectedDays as $day) {
        // تنظیمات موجود برای روز
        $workSchedule = DoctorCounselingWorkSchedule::where('doctor_id', $doctor->id)
          ->where('day', $validated['day'])
          ->first();
        // بازیابی تنظیمات قبلی به صورت آرایه
        $existingSettings = [];
        if ($workSchedule && $workSchedule->appointment_settings) {
          $existingSettings = json_decode($workSchedule->appointment_settings, true);
          if (!is_array($existingSettings)) {
            $existingSettings = [];
          }
        }
        // بررسی اینکه آیا تنظیمی برای این ساعات کاری موجود است
        foreach ($existingSettings as $setting) {
          if (
            ($validated['start_time'] >= $setting['start_time'] && $validated['start_time'] < $setting['end_time']) ||
            ($validated['end_time'] > $setting['start_time'] && $validated['end_time'] <= $setting['end_time']) ||
            ($validated['start_time'] <= $setting['start_time'] && $validated['end_time'] >= $setting['end_time'])
          ) {
            return response()->json([
              'message' => "برای بازه زمانی {$validated['start_time']} تا {$validated['end_time']} در روز " . $this->getDayNameInPersian($validated['day']) . " تنظیماتی وجود دارد.",
              'status' => false
            ], 400);
          }
        }
        // افزودن تنظیم جدید به آرایه تنظیمات موجود
        $newSetting = [
          'start_time' => $validated['start_time'],
          'end_time' => $validated['end_time'],
          'max_appointments' => $validated['max_appointments'],
          'selected_day' => $validated['selected_days']
        ];
        $existingSettings[] = $newSetting;
        // ذخیره تنظیمات جدید به صورت JSON
        DoctorCounselingWorkSchedule::updateOrCreate(
          [
            'doctor_id' => $doctor->id,
            'day' => $validated['day']
          ],
          [
            'is_working' => true,
            'appointment_settings' => json_encode($existingSettings)
          ]
        );
        $results[] = $newSetting;
      }
      return response()->json([
        'message' => 'تنظیمات نوبت‌دهی با موفقیت ذخیره شد.',
        'results' => $results,
        'status' => true
      ]);
    } catch (\Exception $e) {
      return response()->json([
        'message' => 'خطا در ذخیره‌سازی تنظیمات.',
        'status' => false
      ], 500);
    }
  }
  private function calculateMaxAppointments($startTime, $endTime)
  {
    try {
      // تبدیل زمان‌ها به فرمت Carbon
      $start = Carbon::createFromFormat('H:i', $startTime);
      $end = Carbon::createFromFormat('H:i', $endTime);
      // محاسبه تفاوت زمانی به دقیقه
      $diffInMinutes = $start->diffInMinutes($end);
      // تعیین طول هر نوبت (به دقیقه)
      $appointmentDuration = config('settings.default_appointment_duration', 20); // 20 دقیقه پیش‌فرض
      // محاسبه تعداد نوبت‌ها
      return floor($diffInMinutes / $appointmentDuration);
    } catch (\Exception $e) {
      return 0; // بازگرداندن مقدار صفر در صورت بروز خطا
    }
  }
  public function getAppointmentSettings(Request $request)
  {
    $doctor = Auth::guard('doctor')->user() ?? Auth::guard('secretary')->user();
    $workSchedule = DoctorCounselingWorkSchedule::where('doctor_id', $doctor->id)
      ->where('day', $request->day)
      ->first();
    if ($workSchedule && $workSchedule->appointment_settings) {
      return response()->json([
        'settings' => json_decode($workSchedule->appointment_settings, true),
        'day' => $workSchedule->day, // افزودن day به پاسخ
        'status' => true,
      ]);
    }
    return response()->json([
      'message' => 'تنظیماتی یافت نشد',
      'status' => false,
    ]);
  }
  public function saveWorkSchedule(Request $request)
  {
    $validatedData = $request->validate([
      'auto_scheduling' => 'boolean',
      'calendar_days' => 'nullable|integer|min:1|max:365',
      'online_consultation' => 'boolean',
      'holiday_availability' => 'boolean',
      'days' => 'array',
    ]);
    DB::beginTransaction();
    try {
      $doctor = Auth::guard('doctor')->user() ?? Auth::guard('secretary')->user();
      // حذف تنظیمات قبلی
      DoctorCounselingWorkSchedule::where('doctor_id', $doctor->id)->delete();
      // ذخیره تنظیمات کلی نوبت‌دهی
      $appointmentConfig = DoctorCounselingConfig::updateOrCreate(
        ['doctor_id' => $doctor->id],
        [
          'auto_scheduling' => $validatedData['auto_scheduling'] ?? false,
          'calendar_days' => $validatedData['calendar_days'] ?? null,
          'online_consultation' => $validatedData['online_consultation'] ?? false,
          'holiday_availability' => $validatedData['holiday_availability'] ?? false,
        ]
      );
      // ذخیره ساعات کاری پزشک در `work_hours`
      foreach ($validatedData['days'] as $day => $dayConfig) {
        $workHours = isset($dayConfig['slots']) ? array_map(function ($slot) {
          return [
            'start' => $slot['start_time'],
            'end' => $slot['end_time'],
            'max_appointments' => $slot['max_appointments'] ?? 1
          ];
        }, $dayConfig['slots']) : [];
        DoctorCounselingWorkSchedule::create([
          'doctor_id' => $doctor->id,
          'day' => $day,
          'is_working' => $dayConfig['is_working'] ?? false,
          'work_hours' => !empty($workHours) ? json_encode($workHours) : null,
        ]);
      }
      DB::commit();
      return response()->json([
        'message' => 'تنظیمات ساعات کاری با موفقیت ذخیره شد.',
        'status' => true,
        'data' => [
          'calendar_days' => $appointmentConfig->calendar_days
        ]
      ]);
    } catch (\Exception $e) {
      DB::rollBack();
      return response()->json([
        'message' => 'خطا در ذخیره‌سازی تنظیمات ساعات کاری.',
        'status' => false
      ], 500);
    }
  }
  public function getAllDaysSettings(Request $request)
  {
    try {
      $doctor = Auth::guard('doctor')->user() ?? Auth::guard('secretary')->user();
      // دریافت داده‌های ارسال‌شده از درخواست
      $inputDay = $request->input('day');
      $inputStartTime = $request->input('start_time');
      $inputEndTime = $request->input('end_time');
      $inputMaxAppointments = $request->input('max_appointments');
      // فیلتر کردن بر اساس داده‌های ارسال‌شده
      $workSchedules = DoctorCounselingWorkSchedule::where('doctor_id', $doctor->id)
        ->when($inputDay, function ($query) use ($inputDay) {
          $query->where('day', $inputDay);
        })
        ->get();
      $filteredSettings = $workSchedules->map(function ($schedule) use ($inputStartTime, $inputEndTime, $inputMaxAppointments) {
        // تبدیل appointment_settings به آرایه
        $appointmentSettings = [];
        if ($schedule->appointment_settings) {
          if (is_string($schedule->appointment_settings)) {
            $appointmentSettings = json_decode($schedule->appointment_settings, true);
          } elseif (is_array($schedule->appointment_settings)) {
            $appointmentSettings = $schedule->appointment_settings;
          }
        }
        // اگر appointment_settings یک آرایه نباشد، آن را به آرایه خالی تبدیل کنید
        if (!is_array($appointmentSettings)) {
          $appointmentSettings = [];
        }
        // مقایسه با مقادیر ورودی
        if (
          (!$inputStartTime || ($appointmentSettings['start_time'] ?? '') == $inputStartTime) &&
          (!$inputEndTime || ($appointmentSettings['end_time'] ?? '') == $inputEndTime) &&
          (!$inputMaxAppointments || ($appointmentSettings['max_appointments'] ?? '') == $inputMaxAppointments)
        ) {
          return [
            'day' => $schedule->day,
            'start_time' => $appointmentSettings['start_time'] ?? '',
            'end_time' => $appointmentSettings['end_time'] ?? '',
            'max_appointments' => $appointmentSettings['max_appointments'] ?? '',
            'selected_day' => $appointmentSettings['selected_day'] ?? '',
          ];
        }
        return null;
      })->filter(); // حذف مقادیر `null`
      return response()->json([
        'status' => true,
        'settings' => $filteredSettings->values(),
      ]);
    } catch (\Exception $e) {
      return response()->json([
        'status' => false,
        'message' => 'خطا در دریافت تنظیمات.',
      ], 500);
    }
  }
  public function deleteScheduleSetting(Request $request)
  {
    $validated = $request->validate([
      'day' => 'required|in:saturday,sunday,monday,tuesday,wednesday,thursday,friday',
      'selected_day' => 'required|in:saturday,sunday,monday,tuesday,wednesday,thursday,friday',
      'start_time' => 'required|date_format:H:i',
      'end_time' => 'required|date_format:H:i',
    ]);
    try {
      $doctor = Auth::guard('doctor')->user() ?? Auth::guard('secretary')->user();
      // دریافت رکورد مربوط به ساعات کاری پزشک در روز انتخاب‌شده
      $workSchedule = DoctorCounselingWorkSchedule::where('doctor_id', $doctor->id)
        ->where('day', $validated['day'])
        ->first();
      if (!$workSchedule) {
        return response()->json([
          'message' => 'ساعات کاری یافت نشد',
          'status' => false
        ], 404);
      }
      // دیکد کردن تنظیمات نوبت‌دهی (appointment_settings)
      $settings = json_decode($workSchedule->appointment_settings, true) ?? [];
      if (empty($settings)) {
        return response()->json([
          'message' => 'هیچ تنظیماتی برای این روز یافت نشد',
          'status' => false
        ], 404);
      }
      // فیلتر تنظیمات برای حذف آیتم موردنظر
      $updatedSettings = array_filter($settings, function ($setting) use ($validated) {
        return !(
          trim($setting['start_time']) === trim($validated['start_time']) &&  // ✅ استفاده از نام درست فیلد
          trim($setting['end_time']) === trim($validated['end_time']) &&      // ✅ استفاده از نام درست فیلد
          trim($setting['selected_day']) === trim($validated['selected_day']) // ✅ حذف بر اساس `selected_day`
        );
      });
      // بررسی اینکه آیا هیچ تنظیمی حذف شده است یا نه
      if (count($settings) === count($updatedSettings)) {
        return response()->json([
          'message' => 'هیچ تنظیمی حذف نشد. مقدار ارسالی با مقدار ذخیره شده تطابق ندارد.',
          'status' => false
        ], 400);
      }
      // بروزرسانی فیلد `appointment_settings`
      $workSchedule->update(['appointment_settings' => json_encode(array_values($updatedSettings))]);
      return response()->json([
        'message' => 'تنظیم نوبت با موفقیت حذف شد',
        'status' => true
      ]);
    } catch (\Exception $e) {
      return response()->json([
        'message' => 'خطا در حذف تنظیم نوبت: ' . $e->getMessage(),
        'status' => false
      ], 500);
    }
  }
  /**
   * تعیین نوع ساعات کاری بر اساس زمان
   */
  private function determineSlotType($startTime)
  {
    try {
      $hour = intval(substr($startTime, 0, 2));
      if ($hour >= 5 && $hour < 12) {
        return 'morning'; // ساعات کاری صبح
      } elseif ($hour >= 12 && $hour < 17) {
        return 'afternoon'; // ساعات کاری بعد از ظهر
      } else {
        return 'evening'; // ساعات کاری عصر
      }
    } catch (\Exception $e) {
      return 'unknown'; // بازگرداندن مقدار پیش‌فرض در صورت بروز خطا
    }
  }
  /**
   * بازیابی تنظیمات ساعات کاری
   */
  public function getWorkSchedule()
  {
    $doctor = Auth::guard('doctor')->user() ?? Auth::guard('secretary')->user();
    $workSchedules = DoctorCounselingWorkSchedule::where('doctor_id', $doctor->id)
      ->get(); // حذف `with('slots')`
    return response()->json([
      'workSchedules' => $workSchedules
    ]);
  }
  // متدهای موجود در کنترلر اصلی


  public function destroy(Request $request, $slotId)
  {
    try {
      $doctor = Auth::guard('doctor')->user() ?? Auth::guard('secretary')->user();
      // اعتبارسنجی داده‌های ورودی
      $validated = $request->validate([
        'day' => 'required|in:saturday,sunday,monday,tuesday,wednesday,thursday,friday',
        'start_time' => 'required|date_format:H:i',
        'end_time' => 'required|date_format:H:i'
      ]);
      // دریافت رکورد ساعات کاری برای پزشک و روز مورد نظر
      $workSchedule = DoctorCounselingWorkSchedule::where('doctor_id', $doctor->id)
        ->where('day', $validated['day'])
        ->first();
      if (!$workSchedule) {
        return response()->json([
          'message' => 'ساعات کاری یافت نشد',
          'status' => false
        ], 404);
      }
      // دریافت و حذف بازه زمانی مشخص از `work_hours`
      $workHours = json_decode($workSchedule->work_hours, true) ?? [];
      $filteredWorkHours = array_filter($workHours, function ($slot) use ($validated) {
        return !(
          $slot['start'] === $validated['start_time'] &&
          $slot['end'] === $validated['end_time']
        );
      });
      // بررسی اینکه آیا تغییر در داده‌ها رخ داده است
      if (count($filteredWorkHours) === count($workHours)) {
        return response()->json([
          'message' => 'بازه زمانی یافت نشد یا قبلاً حذف شده است',
          'status' => false
        ], 404);
      }
      // ذخیره تغییرات در `doctor_work_schedules`
      $workSchedule->work_hours = json_encode(array_values($filteredWorkHours));
      $workSchedule->save();
      return response()->json([
        'message' => 'بازه زمانی با موفقیت حذف شد',
        'status' => true
      ]);
    } catch (\Exception $e) {
      return response()->json([
        'message' => 'خطا در حذف بازه زمانی',
        'status' => false
      ], 500);
    }
  }
  public function getDefaultSchedule(Request $request)
  {
    $doctorId = Auth::guard('doctor')->id(); // دریافت شناسه پزشک لاگین شده
    $dayOfWeek = $request->input('day_of_week'); // دریافت شماره روز هفته
    // بررسی وجود برنامه کاری برای این روز
    $workSchedule = DoctorCounselingWorkSchedule::where('doctor_id', $doctorId)
      ->where('day', $dayOfWeek) // بررسی روز هفته
      ->first();
    if ($workSchedule && !empty($workSchedule->work_hours)) {
      return response()->json([
        'status' => true,
        'work_hours' => json_decode($workSchedule->work_hours, true) // تبدیل JSON به آرایه
      ]);
    } else {
      return response()->json([
        'status' => false,
        'message' => 'هیچ برنامه کاری‌ای برای این روز تنظیم نشده است.'
      ]);
    }
  }
}
