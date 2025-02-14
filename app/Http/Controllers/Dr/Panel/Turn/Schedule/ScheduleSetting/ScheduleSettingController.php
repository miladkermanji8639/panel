<?php
namespace App\Http\Controllers\Dr\Panel\Turn\Schedule\ScheduleSetting;
use Carbon\Carbon;
use App\Models\User;
use Illuminate\Http\Request;
use App\Models\Dr\Appointment;
use App\Models\Dr\DoctorHoliday;
use App\Models\Dr\AppointmentSlot;
use Illuminate\Support\Facades\DB;
use App\Traits\HandlesRateLimiting;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Models\Dr\DoctorWorkSchedule;
use Illuminate\Support\Facades\Cache;
use App\Models\Dr\SpecialDailySchedule;
use App\Models\Dr\DoctorAppointmentConfig;
use Modules\SendOtp\App\Http\Services\MessageService;
use Modules\SendOtp\App\Http\Services\SMS\SmsService;

class ScheduleSettingController
{
  use HandlesRateLimiting;
  /**
   * نمایش صفحه ساعات کاری
   */
  public function workhours()
  {
    $doctorId = Auth::guard('doctor')->id() ?? Auth::guard('secretary')->id();
    $appointmentConfig = DoctorAppointmentConfig::firstOrCreate(
      ['doctor_id' => $doctorId],
      [
        'auto_scheduling' => true,
        'online_consultation' => false,
        'holiday_availability' => false
      ]
    );
    $workSchedules = DoctorWorkSchedule::where('doctor_id', $doctorId)->get();
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
      $sourceWorkSchedule = DoctorWorkSchedule::where('doctor_id', $doctor->id)
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
        $targetWorkSchedule = DoctorWorkSchedule::firstOrCreate(
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
        'workSchedules' => DoctorWorkSchedule::where('doctor_id', $doctor->id)
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
      $sourceWorkSchedule = DoctorWorkSchedule::where('doctor_id', $doctor->id)
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
        $targetWorkSchedule = DoctorWorkSchedule::firstOrCreate(
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
    $workSchedule = DoctorWorkSchedule::where('doctor_id', $doctor->id)
      ->where('day', $validated['day'])
      ->first();
    // بررسی اینکه آیا ساعات کاری به صورت JSON ذخیره شده است و مقدار دارد
    $hasSlots = $workSchedule && !empty(json_decode($workSchedule->work_hours, true));
    return response()->json(['hasSlots' => $hasSlots]);
  }
  public function saveTimeSlot(Request $request)
  {
    Log::info($request);
    $validated = $request->validate([
      'day' => 'required|in:saturday,sunday,monday,tuesday,wednesday,thursday,friday',
      'start_time' => 'required|date_format:H:i',
      'end_time' => 'required|date_format:H:i|after:start_time',
      'max_appointments' => 'required|integer|min:1'
    ]);

    $doctor = Auth::guard('doctor')->user() ?? Auth::guard('secretary')->user();

    try {
      $workSchedule = DoctorWorkSchedule::firstOrCreate(
        ['doctor_id' => $doctor->id, 'day' => $validated['day']],
        ['is_working' => true, 'work_hours' => json_encode([])]
      );

      $existingWorkHours = json_decode($workSchedule->work_hours, true) ?? [];

      foreach ($existingWorkHours as $hour) {
        $existingStart = Carbon::createFromFormat('H:i', $hour['start']);
        $existingEnd = Carbon::createFromFormat('H:i', $hour['end']);
        $newStart = Carbon::createFromFormat('H:i', $validated['start_time']);
        $newEnd = Carbon::createFromFormat('H:i', $validated['end_time']);

        if ($newStart->equalTo($existingStart) && $newEnd->equalTo($existingEnd)) {
          return response()->json([
            'message' => 'این بازه زمانی از قبل ثبت شده است.',
            'status' => false,
          ], 400);
        }

        if (
          $newStart->between($existingStart, $existingEnd, false) ||
          $newEnd->between($existingStart, $existingEnd, false) ||
          ($newStart->lte($existingStart) && $newEnd->gte($existingEnd))
        ) {
          return response()->json([
            'message' => 'این بازه زمانی با بازه‌های موجود تداخل دارد.',
            'status' => false,
          ], 400);
        }
      }





      // اضافه کردن ساعت جدید به JSON
      $newSlot = [
        'start' => $validated['start_time'],
        'end' => $validated['end_time'],
        'max_appointments' => $validated['max_appointments']
      ];
      $existingWorkHours[] = $newSlot;

      // بروزرسانی `work_hours`
      $workSchedule->update(['work_hours' => json_encode($existingWorkHours)]);

      return response()->json([
        'message' => 'ساعت کاری با موفقیت ذخیره شد',
        'status' => true,
        'work_hours' => $existingWorkHours,
        'workSchedule' => $workSchedule
      ]);
    } catch (\Exception $e) {
      return response()->json([
        'message' => 'خطا در ذخیره‌سازی نوبت',
        'status' => false
      ], 500);
    }
  }
  public function deleteTimeSlot(Request $request)
  {
    $validated = $request->validate([
      'day' => 'required|in:saturday,sunday,monday,tuesday,wednesday,thursday,friday',
      'start_time' => 'required|date_format:H:i',
      'end_time' => 'required|date_format:H:i'
    ]);

    $doctor = Auth::guard('doctor')->user() ?? Auth::guard('secretary')->user();

    try {
      $workSchedule = DoctorWorkSchedule::where('doctor_id', $doctor->id)
        ->where('day', $validated['day'])
        ->first();

      if (!$workSchedule) {
        return response()->json(['message' => 'ساعات کاری یافت نشد', 'status' => false], 404);
      }

      $existingWorkHours = json_decode($workSchedule->work_hours, true) ?? [];

      // فیلتر کردن و حذف ساعت انتخاب‌شده
      $updatedWorkHours = array_filter($existingWorkHours, function ($slot) use ($validated) {
        return !($slot['start'] === $validated['start_time'] && $slot['end'] === $validated['end_time']);
      });

      if (count($existingWorkHours) === count($updatedWorkHours)) {
        return response()->json(['message' => 'ساعت انتخاب‌شده یافت نشد', 'status' => false], 404);
      }

      // بروزرسانی `work_hours`
      $workSchedule->update(['work_hours' => json_encode(array_values($updatedWorkHours))]);

      return response()->json([
        'message' => 'ساعات کاری با موفقیت حذف شد',
        'status' => true
      ]);
    } catch (\Exception $e) {
      return response()->json(['message' => 'خطا در حذف ساعت کاری', 'status' => false], 500);
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
      $workSchedule = DoctorWorkSchedule::updateOrCreate(
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
      $appointmentConfig = DoctorAppointmentConfig::updateOrCreate(
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
        $workSchedule = DoctorWorkSchedule::where('doctor_id', $doctor->id)
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
        $workhours_identifier = $request['workhours_identifier'];

        // افزودن تنظیم جدید به آرایه تنظیمات موجود
        $newSetting = [
          'id' => $workhours_identifier,
          'start_time' => $validated['start_time'],
          'end_time' => $validated['end_time'],
          'max_appointments' => $validated['max_appointments'],
          'selected_day' => $validated['selected_days']
        ];
        $existingSettings[] = $newSetting;
        // ذخیره تنظیمات جدید به صورت JSON
        DoctorWorkSchedule::updateOrCreate(
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

    // دریافت `id` از درخواست
    $id = $request->id;

    // بازیابی تنظیمات نوبت‌دهی برای پزشک
    $workSchedule = DoctorWorkSchedule::where('doctor_id', $doctor->id)
      ->where('day', $request->day)
      ->first();
    if ($workSchedule && $workSchedule->appointment_settings) {
      $settings = json_decode($workSchedule->appointment_settings, true);

      // فیلتر تنظیمات بر اساس `id`
      $filteredSettings = array_filter($settings, function ($setting) use ($id) {
        return $setting['id'] == $id;
      });

      return response()->json([
        'settings' => array_values($filteredSettings), // بازگرداندن تنظیمات فیلتر شده
        'day' => $workSchedule->day,
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
      DoctorWorkSchedule::where('doctor_id', $doctor->id)->delete();
      // ذخیره تنظیمات کلی نوبت‌دهی
      $appointmentConfig = DoctorAppointmentConfig::updateOrCreate(
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
        DoctorWorkSchedule::create([
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
      $workSchedules = DoctorWorkSchedule::where('doctor_id', $doctor->id)
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
      $workSchedule = DoctorWorkSchedule::where('doctor_id', $doctor->id)
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
          trim($setting['start_time']) === trim($validated['start_time']) &&  //  استفاده از نام درست فیلد
          trim($setting['end_time']) === trim($validated['end_time']) &&      //  استفاده از نام درست فیلد
          trim($setting['selected_day']) === trim($validated['selected_day']) //  حذف بر اساس `selected_day`
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
    $workSchedules = DoctorWorkSchedule::where('doctor_id', $doctor->id)
      ->get(); // حذف `with('slots')`
    return response()->json([
      'workSchedules' => $workSchedules
    ]);
  }
  // متدهای موجود در کنترلر اصلی
  public function index()
  {
    return view("dr.panel.turn.schedule.scheduleSetting.index");
  }
  public function turnContract()
  {
    return view("dr.panel.turn.schedule.turnContract.index");
  }
  public function mySpecialDays()
  {
    return view("dr.panel.turn.schedule.scheduleSetting.my-special-days");
  }
  public function getAppointmentsCountPerDay(Request $request)
  {
    try {
      $doctorId = Auth::guard('doctor')->id() ?? Auth::guard('secretary')->id();
      $selectedClinicId = $request->input('selectedClinicId'); // دریافت selectedClinicId از درخواست

      // استخراج تعداد نوبت‌های هر روز
      $appointmentsQuery = DB::table('appointments')
        ->select(DB::raw('appointment_date, COUNT(*) as appointment_count'))
        ->where('doctor_id', $doctorId)
        ->where('status', 'scheduled')
        ->whereNull('deleted_at'); // فیلتر برای نوبت‌های فعال

      // اعمال فیلتر selectedClinicId اگر وجود داشته باشد و برابر 'default' نباشد
      if ($selectedClinicId && $selectedClinicId !== 'default') {
        $appointmentsQuery->where('clinic_id', $selectedClinicId);
      }

      $appointments = $appointmentsQuery
        ->groupBy('appointment_date')
        ->get();

      // ساختاردهی داده‌ها به فرمت دلخواه
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
        'error' => $e->getMessage(), // اضافه کردن خطا برای دیباگ بهتر
      ], 500);
    }
  }
  public function toggleHolidayStatus(Request $request)
  {
    $validated = $request->validate([
      'date' => 'required|date',
      'selectedClinicId' => 'nullable|string',
    ]);
    $doctorId = Auth::guard('doctor')->id() ?? Auth::guard('secretary')->id();
    ;
    $selectedClinicId = $request->input('selectedClinicId');
    // بازیابی یا ایجاد رکورد تعطیلات
    $holidayRecord = DoctorHoliday::firstOrCreate(
      ['doctor_id' => $doctorId],
      ['holiday_dates' => json_encode([])] // مقدار پیش‌فرض آرایه خالی
    );
    if ($selectedClinicId && $selectedClinicId !== 'default') {
      $holidayRecord->where('clinic_id', $selectedClinicId);
    }
    // بررسی و تبدیل JSON به آرایه
    $holidayDates = json_decode($holidayRecord->holiday_dates, true);
    // اطمینان از مقدار آرایه
    if (!is_array($holidayDates)) {
      $holidayDates = [];
    }
    // بررسی وجود تاریخ
    if (in_array($validated['date'], $holidayDates)) {
      // حذف تاریخ در صورت وجود
      $holidayDates = array_diff($holidayDates, [$validated['date']]);
      $message = 'این تاریخ از حالت تعطیلی خارج شد.';
      $isHoliday = false;
    } else {
      // اضافه کردن تاریخ جدید
      $holidayDates[] = $validated['date'];
      $message = 'این تاریخ تعطیل شد.';
      $isHoliday = true;

      $specialDay = SpecialDailySchedule::where('date', $validated['date'])->first();
      if ($selectedClinicId && $selectedClinicId !== 'default') {
        $specialDay->where('clinic_id', $selectedClinicId);
      }
      if ($specialDay) {
        $specialDay->delete();
      }


    }
    // به‌روزرسانی رکورد
    $holidayRecord->update([
      'holiday_dates' => json_encode(array_values($holidayDates)) // ذخیره به صورت JSON
    ]);
    return response()->json([
      'status' => true,
      'is_holiday' => $isHoliday,
      'message' => $message,
      'holiday_dates' => $holidayDates, // ارسال داده‌ها برای دیباگ
    ]);
  }
  public function getHolidayDates(Request $request)
  {
    $doctorId = Auth::guard('doctor')->id() ?? Auth::guard('secretary')->id();
    $selectedClinicId = $request->input('selectedClinicId'); // دریافت selectedClinicId از درخواست

    // جستجوی تعطیلی‌های پزشک
    $holidayQuery = DoctorHoliday::where('doctor_id', $doctorId);

    // اعمال فیلتر selectedClinicId اگر وجود داشته باشد و برابر 'default' نباشد
    if ($selectedClinicId && $selectedClinicId !== 'default') {
      $holidayQuery->where('clinic_id', $selectedClinicId);
    }

    $holidayRecord = $holidayQuery->first();
    $holidays = [];

    // اگر رکورد تعطیلی وجود داشت و تاریخ‌های تعطیلی خالی نبودند
    if ($holidayRecord && !empty($holidayRecord->holiday_dates)) {
      $decodedHolidays = json_decode($holidayRecord->holiday_dates, true);
      $holidays = is_array($decodedHolidays) ? $decodedHolidays : [];
    }

    return response()->json([
      'status' => true,
      'holidays' => $holidays
    ]);
  }
  public function getHolidayStatus(Request $request)
  {
    // اعتبارسنجی ورودی
    $validated = $request->validate([
      'date' => 'required|date',
      'selectedClinicId' => 'nullable|string', // اضافه کردن فیلتر selectedClinicId
    ]);

    $doctorId = Auth::guard('doctor')->id() ?? Auth::guard('secretary')->id();
    $selectedClinicId = $request->input('selectedClinicId');

    // بررسی تعطیلی پزشک
    $holidayRecord = DoctorHoliday::where('doctor_id', $doctorId)->first();
    $holidayDates = json_decode($holidayRecord->holiday_dates ?? '[]', true);
    $isHoliday = in_array($validated['date'], $holidayDates);

    // گرفتن نوبت‌های پزشک در تاریخ مشخص
    $appointmentsQuery = Appointment::where('doctor_id', $doctorId)
      ->where('appointment_date', $validated['date']);

    // اعمال فیلتر selectedClinicId اگر وجود داشته باشد و برابر 'default' نباشد
    if ($selectedClinicId && $selectedClinicId !== 'default') {
      $appointmentsQuery->where('clinic_id', $selectedClinicId);
    }

    $appointments = $appointmentsQuery->get();

    return response()->json([
      'status' => true,
      'is_holiday' => $isHoliday,
      'data' => $appointments
    ]);
  }
  public function cancelAppointments(Request $request)
  {
    $validatedData = $request->validate([
      'date' => 'required|date',
      'selectedClinicId' => 'nullable|string',
    ]);

    // دریافت تمام نوبت‌ها برای تاریخ مشخص
    $appointments = Appointment::where('appointment_date', $validatedData['date'])->get();
    $selectedClinicId = $request->input('selectedClinicId');
    if ($selectedClinicId && $selectedClinicId !== 'default') {
      $appointments->where('clinic_id', $selectedClinicId);
    }
    foreach ($appointments as $appointment) {
      $appointment->status = 'cancelled';
      $appointment->deleted_at = now(); // حذف نرم‌افزاری
      $appointment->save();
    }

    return response()->json([
      'status' => true,
      'message' => 'نوبت‌ها با موفقیت لغو شدند.',
    ]);
  }

  public function rescheduleAppointment(Request $request)
  {
    // اعتبارسنجی ورودی
    $validated = $request->validate([
      'old_date' => 'required|date', // تاریخ قبلی نوبت
      'new_date' => 'required|date', // تاریخ جدید نوبت
      'selectedClinicId' => 'nullable|string', // اضافه کردن فیلتر selectedClinicId
    ]);

    $doctorId = Auth::guard('doctor')->id() ?? Auth::guard('secretary')->id();
    $selectedClinicId = $request->input('selectedClinicId');

    try {
      // پیدا کردن تمام نوبت‌های آن تاریخ
      $appointmentsQuery = Appointment::where('doctor_id', $doctorId)
        ->where('appointment_date', $validated['old_date']);

      // اعمال فیلتر selectedClinicId اگر وجود داشته باشد و برابر 'default' نباشد
      if ($selectedClinicId && $selectedClinicId !== 'default') {
        $appointmentsQuery->where('clinic_id', $selectedClinicId);
      }

      $appointments = $appointmentsQuery->get();

      if ($appointments->isEmpty()) {
        return response()->json([
          'status' => false,
          'message' => 'هیچ نوبتی برای این تاریخ یافت نشد.',
        ], 404);
      }

      // بررسی ساعات کاری پزشک برای تاریخ جدید
      $selectedDate = Carbon::parse($validated['new_date']); // تبدیل تاریخ جدید به میلادی
      $dayOfWeek = strtolower($selectedDate->format('l'));
      $workHours = DoctorWorkSchedule::where('day', $dayOfWeek)->first();
      if (!$workHours || !$workHours->work_hours) {
        return response()->json([
          'status' => false,
          'message' => 'ساعات کاری برای این روز یافت نشد.',
        ], 400);
      }

      // لیست شماره‌های موبایل کاربران
      $recipients = [];

      foreach ($appointments as $appointment) {
        $oldDate = $appointment->appointment_date;
        $appointment->appointment_date = $validated['new_date'];
        $appointment->save();

        // اضافه کردن شماره موبایل به لیست دریافت‌کنندگان پیامک
        if ($appointment->patient && $appointment->patient->mobile) {
          $recipients[] = $appointment->patient->mobile;
        }
      }

      $oldDateJalali = \Morilog\Jalali\Jalalian::fromDateTime($validated['old_date'])->format('Y/m/d');
      $newDateJalali = \Morilog\Jalali\Jalalian::fromDateTime($validated['new_date'])->format('Y/m/d');

      // ارسال پیامک به همه کاربران
      if (!empty($recipients)) {
        $messageContent = "کاربر گرامی، نوبت شما از تاریخ {$oldDateJalali} به تاریخ {$newDateJalali} تغییر یافت.";

        foreach ($recipients as $recipient) {
          $userFullName = User::where('mobile', $recipient)->first();
          $userFullName = $userFullName->first_name . " " . $userFullName->last_name;
          $messagesService = new MessageService(
            SmsService::create(100252, $recipient, [$userFullName, $oldDateJalali, $newDateJalali, 'به نوبه'])
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

  public function getNextAvailableDate(Request $request)
  {
    $doctorId = Auth::guard('doctor')->id() ?? Auth::guard('secretary')->id();
    $selectedClinicId = $request->input('selectedClinicId'); // دریافت selectedClinicId از درخواست

    // دریافت تعطیلی‌های پزشک
    $holidaysQuery = DoctorHoliday::where('doctor_id', $doctorId);

    // اعمال فیلتر selectedClinicId اگر وجود داشته باشد و برابر 'default' نباشد
    if ($selectedClinicId && $selectedClinicId !== 'default') {
      $holidaysQuery->where('clinic_id', $selectedClinicId);
    }

    $holidays = $holidaysQuery->first();
    $holidayDates = json_decode($holidays->holiday_dates ?? '[]', true);

    $today = Carbon::now()->startOfDay();
    $daysToCheck = DoctorAppointmentConfig::where('doctor_id', $doctorId)->value('calendar_days') ?? 30;

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
      $appointmentQuery = Appointment::where('doctor_id', $doctorId)
        ->where('appointment_date', $date);

      // اعمال فیلتر selectedClinicId اگر وجود داشته باشد و برابر 'default' نباشد
      if ($selectedClinicId && $selectedClinicId !== 'default') {
        $appointmentQuery->where('clinic_id', $selectedClinicId);
      }

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
      // پیدا کردن تمام نوبت‌های اولین تاریخ ثبت‌شده
      $appointmentsQuery = Appointment::where('doctor_id', $doctorId)
        ->where('appointment_date', $validated['old_date']);

      // اعمال فیلتر selectedClinicId اگر وجود داشته باشد و برابر 'default' نباشد
      if ($selectedClinicId && $selectedClinicId !== 'default') {
        $appointmentsQuery->where('clinic_id', $selectedClinicId);
      }

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
      $workHours = DoctorWorkSchedule::where('day', $dayOfWeek)->first();

      if (!$workHours || !$workHours->workhours) {
        return response()->json([
          'status' => false,
          'message' => 'ساعات کاری پزشک برای تاریخ جدید یافت نشد.',
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

      $oldDateJalali = \Morilog\Jalali\Jalalian::fromDateTime($validated['old_date'])->format('Y/m/d');
      $newDateJalali = \Morilog\Jalali\Jalalian::fromDateTime($validated['new_date'])->format('Y/m/d');

      // ارسال پیامک به همه کاربران
      if (!empty($recipients)) {
        $messageContent = "کاربر گرامی، نوبت شما از تاریخ {$oldDateJalali} به تاریخ {$newDateJalali} تغییر یافت.";

        foreach ($recipients as $recipient) {
          $userFullName = User::where('mobile', $recipient)->first();
          $userFullName = $userFullName->first_name . " " . $userFullName->last_name;
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


  public function getAppointmentsByDate(Request $request)
  {
    $date = $request->input('date'); // تاریخ به فرمت میلادی
    $selectedClinicId = $request->selectedClinicId;

    // بررسی وجود نوبت برای تاریخ مورد نظر
    $appointments = Appointment::where('appointment_date', $date)
      ->where('status', 'scheduled')
      ->get();
    // اعمال فیلتر selectedClinicId
    if ($selectedClinicId === 'default') {
      // اگر selectedClinicId برابر با 'default' باشد، clinic_id باید NULL یا خالی باشد
      $appointments->whereNull('clinic_id');
    } elseif ($selectedClinicId) {
      // اگر selectedClinicId مقدار داشت، clinic_id باید با آن مطابقت داشته باشد
      $appointments->where('clinic_id', $selectedClinicId);
    }

    // بررسی اگر هیچ نوبتی وجود ندارد
    $isHoliday = $appointments->isEmpty();
    return response()->json([
      'status' => true,
      'is_holiday' => $isHoliday,
      'data' => $appointments, // اگر نوبت وجود داشته باشد، ارسال می‌شود
    ]);
  }
  public function addHoliday(Request $request)
  {
    $validated = $request->validate([
      'date' => 'required|date', // تاریخ به فرمت میلادی
    ]);
    try {
      $doctorId = Auth::guard('doctor')->id() ?? Auth::guard('secretary')->id();
      ;
      // بررسی وجود تعطیلی برای همان تاریخ
      $existingHoliday = DoctorHoliday::where('doctor_id', $doctorId)
        ->where('holiday_dates', $validated['date'])
        ->first();
      if ($existingHoliday) {
        return response()->json(['status' => false, 'message' => 'این تاریخ قبلاً به عنوان تعطیل ثبت شده است.']);
      }
      // ذخیره تعطیلی در جدول
      DoctorHoliday::create([
        'doctor_id' => $doctorId,
        'holiday_dates' => $validated['date'],
      ]);
      return response()->json(['status' => true, 'message' => 'روز موردنظر به‌عنوان تعطیل ثبت شد.']);
    } catch (\Exception $e) {
      return response()->json(['status' => false, 'message' => 'خطا در ثبت تعطیلی.'], 500);
    }
  }
  public function toggleHoliday(Request $request)
  {

    $validated = $request->validate([
      'date' => 'required|date', // تاریخ به فرمت میلادی
    ]);

    $doctorId = Auth::guard('doctor')->id() ?? Auth::guard('secretary')->id();
    ;
    $holidayRecord = DoctorHoliday::firstOrCreate(
      ['doctor_id' => $doctorId],
      ['holiday_dates' => json_encode([])]
    );
    $holidayDates = json_decode($holidayRecord->holiday_dates, true) ?? [];
    if (in_array($validated['date'], $holidayDates)) {
      $holidayDates = array_diff($holidayDates, [$validated['date']]);
      $message = 'این تاریخ از حالت تعطیلی خارج شد.';
      $isHoliday = false;
    } else {
      $holidayDates[] = $validated['date'];
      $message = 'این تاریخ تعطیل شد.';
      $isHoliday = true;
    }
    $holidayRecord->holiday_dates = json_encode(array_values($holidayDates));
    $holidayRecord->save();
    return response()->json([
      'status' => true,
      'is_holiday' => $isHoliday,
      'message' => $message,
      'holiday_dates' => $holidayDates,
    ]);
  }
  public function getHolidays(Request $request)
  {
    try {
      $doctorId = Auth::guard('doctor')->id() ?? Auth::guard('secretary')->id();
      ;
      $holidays = DoctorHoliday::where('doctor_id', $doctorId)->get(['holiday_dates']);
      return response()->json([
        'status' => true,
        'holidays' => $holidays,
      ]);
    } catch (\Exception $e) {
      return response()->json([
        'status' => false,
        'message' => 'خطا در دریافت داده‌ها.',
      ], 500);
    }
  }

  public function destroy(Request $request)
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
      $workSchedule = DoctorWorkSchedule::where('doctor_id', $doctor->id)
        ->where('day', $validated['day'])
        ->first();

      if (!$workSchedule) {
        return response()->json([
          'message' => 'ساعات کاری یافت نشد',
          'status' => false
        ], 404);
      }

      // بررسی مقدار `work_hours` قبل از حذف
      $workHours = json_decode($workSchedule->work_hours, true);

      if (!is_array($workHours)) {
        Log::error('❌ مقدار `work_hours` نامعتبر است:', ['work_hours' => $workSchedule->work_hours]);
        return response()->json([
          'message' => 'خطا در پردازش ساعات کاری',
          'status' => false
        ], 500);
      }

      // 🟢 لاگ مقدار اولیه قبل از حذف
      Log::info('🔍 مقدار اولیه `work_hours`:', ['work_hours' => $workHours]);

      // فیلتر بازه زمانی مشخص از `work_hours`
      $filteredWorkHours = array_filter($workHours, function ($slot) use ($validated) {
        return !(
          trim((string) $slot['start']) === trim((string) $validated['start_time']) &&
          trim((string) $slot['end']) === trim((string) $validated['end_time'])
        );
      });

      // 🟢 لاگ مقدار بعد از حذف بازه
      Log::info('📌 مقدار `work_hours` بعد از حذف:', ['filtered_work_hours' => $filteredWorkHours]);

      // بررسی اینکه آیا تغییری رخ داده است
      if (count($filteredWorkHours) === count($workHours)) {
        return response()->json([
          'message' => 'بازه زمانی یافت نشد یا قبلاً حذف شده است',
          'status' => false
        ], 404);
      }

      // ذخیره تغییرات در `doctor_work_schedules`
      $workSchedule->work_hours = empty($filteredWorkHours) ? null : json_encode(array_values($filteredWorkHours));

      if (!$workSchedule->save()) {
        Log::error('❌ خطا در ذخیره تغییرات در پایگاه داده');
        return response()->json([
          'message' => 'خطا در ذخیره تغییرات',
          'status' => false
        ], 500);
      }

      return response()->json([
        'message' => ' بازه زمانی با موفقیت حذف شد',
        'status' => true
      ]);
    } catch (\Exception $e) {
      Log::error('❌ خطای حذف بازه زمانی:', ['error' => $e->getMessage()]);
      return response()->json([
        'message' => 'خطا در حذف بازه زمانی',
        'status' => false
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
    $specialScheduleQuery = SpecialDailySchedule::where('date', $date);
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
    $workScheduleQuery = DoctorWorkSchedule::where('doctor_id', $doctorId)
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
  public function getWorkHours(Request $request)
  {
    $doctorId = Auth::guard('doctor')->user()->id;
    $date = $request->input('date');

    // بررسی جدول جدید (special_daily_schedules)
    $specialSchedule = SpecialDailySchedule::where('doctor_id', $doctorId)
      ->where('date', $date)
      ->first();

    if ($specialSchedule) {
      return response()->json([
        'status' => true,
        'source' => 'special_daily_schedules',
        'work_hours' => $specialSchedule->work_hours
      ]);
    }

    // بررسی جدول قدیمی (doctor_work_schedules)
    $defaultSchedule = DoctorWorkSchedule::where('doctor_id', $doctorId)
      ->where('day_of_week', date('w', strtotime($date)))
      ->first();

    if ($defaultSchedule) {
      return response()->json([
        'status' => true,
        'source' => 'doctor_work_schedules',
        'work_hours' => json_decode($defaultSchedule->work_hours, true)
      ]);
    }

    return response()->json(['status' => false, 'message' => 'هیچ ساعت کاری برای این روز ثبت نشده است.']);
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
    $specialWorkHoursQuery = SpecialDailySchedule::where('date', $date);

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
      SpecialDailySchedule::create([
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


}