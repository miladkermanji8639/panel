<?php
namespace App\Http\Controllers\Dr\Panel\Turn\Schedule\ScheduleSetting;
use Carbon\Carbon;
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
use App\Models\Dr\DoctorAppointmentConfig;
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
      'override' => 'nullable', // اجازه به null یا مقدار بولی
    ]);
    $doctor = Auth::guard('doctor')->user() ?? Auth::guard('secretary')->user();

    DB::beginTransaction();
    try {
      // بازیابی برنامه کاری روز مبدأ
      $sourceWorkSchedule = DoctorWorkSchedule::where('doctor_id', $doctor->id)
        ->where('day', $validated['source_day'])
        ->first();
      if (!$sourceWorkSchedule) {
        return response()->json([
          'message' => 'روز مبدأ یافت نشد',
          'status' => false
        ], 404);
      }
      // بازیابی اسلات‌های روز مبدأ
      $sourceSlots = AppointmentSlot::where('work_schedule_id', $sourceWorkSchedule->id)->get();
      if ($sourceSlots->isEmpty()) {
        return response()->json([
          'message' => 'زمانی برای کپی وجود ندارد. لطفاً ابتدا یک زمان اضافه کنید.',
          'status' => false
        ], 400);
      }
      // استخراج work_hours از اسلات‌ها
      $workHours = $sourceSlots->map(function ($slot) {
        $timeSlots = $slot->time_slots;
        return [
          'start' => $timeSlots['start_time'] ?? $timeSlots['start'],
          'end' => $timeSlots['end_time'] ?? $timeSlots['end'],
          'max_appointments' => $slot->max_appointments
        ];
      })->toArray();
      foreach ($validated['target_days'] as $targetDay) {
        $targetWorkSchedule = DoctorWorkSchedule::firstOrCreate(
          [
            'doctor_id' => $doctor->id,
            'day' => $targetDay,
          ],
          [
            'is_working' => true,
          ]
        );
        if (!empty($validated['override'])) {
          // حذف اسلات‌های قبلی در صورت درخواست جایگزینی
          AppointmentSlot::where('work_schedule_id', $targetWorkSchedule->id)->delete();
        } else {
          // بررسی تداخل زمانی
          foreach ($sourceSlots as $sourceSlot) {
            $sourceStart = Carbon::createFromFormat('H:i', $sourceSlot->time_slots['start_time']);
            $sourceEnd = Carbon::createFromFormat('H:i', $sourceSlot->time_slots['end_time']);
            foreach ($targetWorkSchedule->slots as $existingSlot) {
              $existingStart = Carbon::createFromFormat('H:i', $existingSlot->time_slots['start_time']);
              $existingEnd = Carbon::createFromFormat('H:i', $existingSlot->time_slots['end_time']);
              if (
                ($sourceStart >= $existingStart && $sourceStart < $existingEnd) ||
                ($sourceEnd > $existingStart && $sourceEnd <= $existingEnd) ||
                ($sourceStart <= $existingStart && $sourceEnd >= $existingEnd)
              ) {
                return response()->json([
                  'message' => 'بازه زمانی ' . $sourceStart->format('H:i') . ' تا ' . $sourceEnd->format('H:i') . ' با بازه‌های موجود تداخل دارد.',
                  'status' => false,
                  'day' => $targetDay,
                  'conflicting_slots' => [
                    'source_start' => $sourceStart->format('H:i'),
                    'source_end' => $sourceEnd->format('H:i'),
                    'existing_start' => $existingStart->format('H:i'),
                    'existing_end' => $existingEnd->format('H:i'),
                  ]
                ], 400);
              }
            }
          }
        }
        // ایجاد اسلات‌های جدید
        foreach ($sourceSlots as $sourceSlot) {
          AppointmentSlot::create([
            'work_schedule_id' => $targetWorkSchedule->id,
            'time_slots' => $sourceSlot->time_slots,
            'max_appointments' => $sourceSlot->max_appointments,
            'is_active' => $sourceSlot->is_active,
          ]);
        }
      }
      DB::commit();
      return response()->json([
        'message' => 'ساعات کاری با موفقیت کپی شد',
        'status' => true,
        'target_days' => $validated['target_days'],
        'workSchedules' => DoctorWorkSchedule::where('doctor_id', $doctor->id)
          ->whereIn('day', $validated['target_days'])
          ->with('slots') // ارتباط با اسلات‌ها
          ->get()
      ]);
    } catch (\Exception $e) {
      DB::rollBack();
      Log::error('خطا در کپی ساعات کاری: ' . $e->getMessage(), [
        'doctor_id' => $doctor->id ?? null,
        'trace' => $e->getTraceAsString(),
      ]);
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
      'slot_id' => 'required|exists:appointment_slots,id',
      'override' => 'nullable|in:0,1,true,false'
    ]);
    $override = filter_var($request->input('override', false), FILTER_VALIDATE_BOOLEAN);
    $doctor = Auth::guard('doctor')->user() ?? Auth::guard('secretary')->user();

    DB::beginTransaction();
    try {
      $sourceSlot = AppointmentSlot::findOrFail($validated['slot_id']);
      $conflictingSlots = [];
      foreach ($validated['target_days'] as $targetDay) {
        $targetWorkSchedule = DoctorWorkSchedule::firstOrCreate(
          [
            'doctor_id' => $doctor->id,
            'day' => $targetDay
          ],
          ['is_working' => true]
        );
        $existingSlots = AppointmentSlot::where('work_schedule_id', $targetWorkSchedule->id)->get();
        foreach ($existingSlots as $existingSlot) {
          $existingStart = Carbon::createFromFormat('H:i', $existingSlot->time_slots['start_time']);
          $existingEnd = Carbon::createFromFormat('H:i', $existingSlot->time_slots['end_time']);
          $newStart = Carbon::createFromFormat('H:i', $sourceSlot->time_slots['start_time']);
          $newEnd = Carbon::createFromFormat('H:i', $sourceSlot->time_slots['end_time']);
          if (
            ($newStart >= $existingStart && $newStart < $existingEnd) ||
            ($newEnd > $existingStart && $newEnd <= $existingEnd) ||
            ($newStart <= $existingStart && $newEnd >= $existingEnd)
          ) {
            if (!$override) {
              $conflictingSlots[] = [
                'day' => $this->getDayNameInPersian($targetDay),
                'start' => $existingStart->format('H:i'),
                'end' => $existingEnd->format('H:i')
              ];
            }
          }
        }
      }
      if (!empty($conflictingSlots)) {
        return response()->json([
          'message' => 'تداخل زمانی وجود دارد',
          'conflicting_slots' => $conflictingSlots,
          'status' => false
        ], 400);
      }
      foreach ($validated['target_days'] as $targetDay) {
        $targetWorkSchedule = DoctorWorkSchedule::firstOrCreate(
          [
            'doctor_id' => $doctor->id,
            'day' => $targetDay
          ],
          ['is_working' => true]
        );
        if ($override) {
          // حذف فقط اسلات‌های متداخل
          AppointmentSlot::where('work_schedule_id', $targetWorkSchedule->id)
            ->where(function ($query) use ($sourceSlot) {
              $query->whereRaw("JSON_EXTRACT(time_slots, '$.start_time') = ?", [$sourceSlot->time_slots['start_time']])
                ->orWhereRaw("JSON_EXTRACT(time_slots, '$.end_time') = ?", [$sourceSlot->time_slots['end_time']]);
            })
            ->delete();
        }
        AppointmentSlot::create([
          'work_schedule_id' => $targetWorkSchedule->id,
          'time_slots' => $sourceSlot->time_slots,
          'max_appointments' => $sourceSlot->max_appointments,
          'is_active' => $sourceSlot->is_active
        ]);
      }
      DB::commit();
      return response()->json([
        'message' => 'اسلات با موفقیت کپی شد',
        'status' => true,
        'target_days' => $validated['target_days']
      ]);
    } catch (\Exception $e) {
      DB::rollBack();
      Log::error('خطا در کپی اسلات: ' . $e->getMessage());
      return response()->json([
        'message' => 'خطا در کپی اسلات',
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
      'day' => 'required|string'
    ]);
    $doctor = Auth::guard('doctor')->user() ?? Auth::guard('secretary')->user();

    $workSchedule = DoctorWorkSchedule::where('doctor_id', $doctor->id)
      ->where('day', $validated['day'])
      ->first();

    $hasSlots = $workSchedule ? AppointmentSlot::where('work_schedule_id', $workSchedule->id)->exists() : false;

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
      // بازیابی یا ایجاد رکورد WorkSchedule
      $workSchedule = DoctorWorkSchedule::firstOrCreate(
        [
          'doctor_id' => $doctor->id,
          'day' => $validated['day'],
        ],
        [
          'is_working' => true,
          'appointment_settings' => json_encode([
            'start_time' => $validated['start_time'],
            'end_time' => $validated['end_time'],
            'max_appointments' => $validated['max_appointments']
          ]),
        ]
      );
      // بررسی تداخل زمانی
      $existingSlots =
        AppointmentSlot::whereHas('workSchedule', function ($query) use ($doctor, $validated) {
          $query->where('doctor_id', $doctor->id)
            ->where('day', $validated['day']);
        })->get();
      foreach ($existingSlots as $slot) {
        $slotStart = Carbon::createFromFormat('H:i', $slot->time_slots['start_time']);
        $slotEnd = Carbon::createFromFormat('H:i', $slot->time_slots['end_time']);
        $newStart = Carbon::createFromFormat('H:i', $validated['start_time']);
        $newEnd = Carbon::createFromFormat('H:i', $validated['end_time']);
        if (
          ($newStart >= $slotStart && $newStart < $slotEnd) ||
          ($newEnd > $slotStart && $newEnd <= $slotEnd) ||
          ($newStart <= $slotStart && $newEnd >= $slotEnd)
        ) {
          return response()->json([
            'message' => 'این بازه زمانی با بازه‌های موجود تداخل دارد.',
            'status' => false
          ], 400);
        }
      }
      // ایجاد اسلات جدید
      $slot = AppointmentSlot::create([
        'work_schedule_id' => $workSchedule->id,
        'time_slots' => [
          'start_time' => $validated['start_time'],
          'end_time' => $validated['end_time']
        ],
        'max_appointments' => $validated['max_appointments'],
        'is_active' => true
      ]);
      // به‌روزرسانی کش
      return response()->json([
        'message' => 'موفقیت آمیز',
        'slot_id' => $slot->id,
        'status' => true
      ]);
    } catch (\Exception $e) {
      Log::error('خطا در ذخیره‌سازی: ' . $e->getMessage());
      return response()->json([
        'message' => 'خطا در ذخیره‌سازی',
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
      Log::error('خطا در بروزرسانی وضعیت روز کاری: ' . $e->getMessage());
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
      Log::error('خطا در به‌روزرسانی وضعیت نوبت‌دهی خودکار: ' . $e->getMessage());
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
        // بررسی اینکه آیا تنظیمی برای این اسلات موجود است
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
      Log::error('خطا در ذخیره‌سازی تنظیمات نوبت‌دهی: ' . $e->getMessage());
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
      Log::error('خطا در محاسبه تعداد نوبت‌ها: ' . $e->getMessage(), [
        'startTime' => $startTime,
        'endTime' => $endTime
      ]);
      return 0; // بازگرداندن مقدار صفر در صورت بروز خطا
    }
  }
  public function getAppointmentSettings(Request $request)
  {
    $doctor = Auth::guard('doctor')->user() ?? Auth::guard('secretary')->user();

    $workSchedule = DoctorWorkSchedule::where('doctor_id', $doctor->id)
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
      DoctorWorkSchedule::where('doctor_id', $doctor->id)->delete();
      AppointmentSlot::whereHas('workSchedule', function ($query) use ($doctor) {
        $query->where('doctor_id', $doctor->id);
      })->delete();
      // ذخیره تنظیمات کلی
      $appointmentConfig = DoctorAppointmentConfig::updateOrCreate(
        ['doctor_id' => $doctor->id],
        [
          'auto_scheduling' => $validatedData['auto_scheduling'] ?? false,
          'calendar_days' => $request->input('calendar_days'),
          'online_consultation' => $validatedData['online_consultation'] ?? false,
          'holiday_availability' => $validatedData['holiday_availability'] ?? false,
        ]
      );
      // ذخیره برنامه کاری روزها
      foreach ($validatedData['days'] as $day => $dayConfig) {
        $workSchedule = DoctorWorkSchedule::create([
          'doctor_id' => $doctor->id,
          'day' => $day,
          'is_working' => $dayConfig['is_working'] ?? false,
          'work_hours' => $dayConfig['work_hours'] ?? null,
        ]);
        // ذخیره اسلات‌ها
        if (isset($dayConfig['slots']) && is_array($dayConfig['slots'])) {
          foreach ($dayConfig['slots'] as $slot) {
            AppointmentSlot::create([
              'work_schedule_id' => $workSchedule->id,
              'start_time' => $slot['start_time'],
              'end_time' => $slot['end_time'],
              'max_appointments' => $slot['max_appointments'] ?? 1,
              'slot_type' => $this->determineSlotType($slot['start_time']),
              'is_active' => true
            ]);
          }
        }
      }
      DB::commit();
      return response()->json([
        'message' => 'تنظیمات با موفقیت ذخیره شد',
        'status' => true,
        'data' => [
          'calendar_days' => $appointmentConfig->calendar_days
        ]
      ]);
    } catch (\Exception $e) {
      DB::rollBack();
      Log::error('خطا در ذخیره‌سازی تنظیمات: ' . $e->getMessage(), [
        'trace' => $e->getTraceAsString()
      ]);
      return response()->json([
        'message' => 'خطا در ذخیره‌سازی تنظیمات: ' . $e->getMessage(),
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
      Log::error('خطا در دریافت تنظیمات همه روزها: ' . $e->getMessage());
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
      'start_time' => 'required|date_format:H:i',
      'end_time' => 'required|date_format:H:i',
    ]);
    Log::info($request);
    try {
      $doctor = Auth::guard('doctor')->user() ?? Auth::guard('secretary')->user();
      DB::beginTransaction();

      $workSchedule = DoctorWorkSchedule::where('doctor_id', $doctor->id)
        ->where('day', $validated['day'])
        ->first();

      if ($workSchedule && $workSchedule->appointment_settings) {
        $settings = json_decode($workSchedule->appointment_settings, true);
        $settings = array_filter($settings, function ($setting) use ($validated) {
          return !(
            $setting['start_time'] === $validated['start_time'] &&
            $setting['end_time'] === $validated['end_time']
          );
        });

        $workSchedule->appointment_settings = json_encode(array_values($settings));
        $workSchedule->save();
        DB::commit();

        return response()->json([
          'message' => 'تنظیمات با موفقیت حذف شد',
          'status' => true,
        ]);
      } else {
        return response()->json([
          'message' => 'تنظیمات یافت نشد',
          'status' => false,
        ], 404);
      }
    } catch (\Exception $e) {
      DB::rollBack();
      Log::error('خطا در حذف تنظیمات: ' . $e->getMessage());
      return response()->json([
        'message' => 'خطا در حذف تنظیمات',
        'status' => false,
      ], 500);
    }
  }

  /**
   * تعیین نوع اسلات بر اساس زمان
   */
  private function determineSlotType($startTime)
  {
    try {
      $hour = intval(substr($startTime, 0, 2));
      if ($hour >= 5 && $hour < 12) {
        return 'morning'; // اسلات صبح
      } elseif ($hour >= 12 && $hour < 17) {
        return 'afternoon'; // اسلات بعد از ظهر
      } else {
        return 'evening'; // اسلات عصر
      }
    } catch (\Exception $e) {
      Log::error('خطا در تعیین نوع اسلات: ' . $e->getMessage());
      return 'unknown'; // بازگرداندن مقدار پیش‌فرض در صورت بروز خطا
    }
  }
  /**
   * بازیابی تنظیمات ساعات کاری
   */
  public function getWorkSchedule()
  {
    $doctor = Auth::guard('doctor')->user() ?? Auth::guard('secretary')->user();

    $appointmentConfig = DoctorAppointmentConfig::where('doctor_id', $doctor->id)->first();
    $workSchedules = DoctorWorkSchedule::with('slots')
      ->where('doctor_id', $doctor->id)
      ->get();

    return response()->json([
      'appointmentConfig' => $appointmentConfig,
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
      $doctorId = Auth::guard('doctor')->id() ?? Auth::guard('secretary')->id();;
      // استخراج تعداد نوبت‌های هر روز
      $appointments = DB::table('appointments')
        ->select(DB::raw('appointment_date, COUNT(*) as appointment_count'))
        ->where('doctor_id', $doctorId)
        ->where('status', 'scheduled') // فیلتر برای نوبت‌های فعال
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
      Log::error('خطا در دریافت تعداد نوبت‌ها: ' . $e->getMessage());
      return response()->json([
        'status' => false,
        'message' => 'خطا در دریافت داده‌ها',
      ], 500);
    }
  }
  public function toggleHolidayStatus(Request $request)
  {
    $validated = $request->validate([
      'date' => 'required|date', // تاریخ به فرمت میلادی
    ]);
    $doctorId = Auth::guard('doctor')->id() ?? Auth::guard('secretary')->id();;
    // بازیابی یا ایجاد رکورد تعطیلات
    $holidayRecord = DoctorHoliday::firstOrCreate(
      ['doctor_id' => $doctorId],
      ['holiday_dates' => json_encode([])] // مقدار پیش‌فرض آرایه خالی
    );
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
  public function getHolidayDates()
  {
    $doctorId = Auth::guard('doctor')->id() ?? Auth::guard('secretary')->id();;
    $holidayRecord = DoctorHoliday::where('doctor_id', $doctorId)->first();
    $holidays = [];
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
    $validated = $request->validate([
      'date' => 'required|date',
    ]);
    $doctorId = Auth::guard('doctor')->id() ?? Auth::guard('secretary')->id();

    $holidayRecord = DoctorHoliday::where('doctor_id', $doctorId)->first();
    $holidayDates = json_decode($holidayRecord->holiday_dates ?? '[]', true);
    $isHoliday = in_array($validated['date'], $holidayDates);

    $appointments = Appointment::where('doctor_id', $doctorId)
      ->where('appointment_date', $validated['date'])
      ->get();

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
    ]);
    Appointment::where('appointment_date', $validatedData['date'])->delete();
    return response()->json([
      'status' => true,
      'message' => 'تمام نوبت‌ها با موفقیت لغو شدند.',
    ]);
  }
  public function rescheduleAppointment(Request $request)
  {
    $validated = $request->validate([
      'old_date' => 'required|date', // تاریخ قبلی نوبت
      'new_date' => 'required|date', // تاریخ جدید نوبت
    ]);
    $doctorId = Auth::guard('doctor')->id() ?? Auth::guard('secretary')->id();;
    try {
      // پیدا کردن نوبت با تاریخ قبلی
      $appointment = Appointment::where('doctor_id', $doctorId)
        ->where('appointment_date', $validated['old_date'])
        ->first();
      if (!$appointment) {
        return response()->json([
          'status' => false,
          'message' => 'نوبت موردنظر برای جابجایی یافت نشد.',
        ], 404);
      }
      // جابجایی به تاریخ جدید
      $appointment->appointment_date = $validated['new_date'];
      $appointment->save();
      return response()->json([
        'status' => true,
        'message' => 'نوبت با موفقیت جابجا شد.',
        'data' => $appointment,
      ]);
    } catch (\Exception $e) {
      Log::error('خطا در جابجایی نوبت: ' . $e->getMessage());
      return response()->json([
        'status' => false,
        'message' => 'خطا در جابجایی نوبت.',
      ], 500);
    }
  }
  public function getNextAvailableDate()
  {
    $doctorId = Auth::guard('doctor')->id() ?? Auth::guard('secretary')->id();
    $holidays = DoctorHoliday::where('doctor_id', $doctorId)->first();
    $holidayDates = json_decode($holidays->holiday_dates ?? '[]', true);

    $today = Carbon::now()->startOfDay();
    $daysToCheck = DoctorAppointmentConfig::where('doctor_id', $doctorId)->value('calendar_days') ?? 30;

    $datesToCheck = collect();
    for ($i = 1; $i <= $daysToCheck; $i++) {
      $date = $today->copy()->addDays($i)->format('Y-m-d');
      $datesToCheck->push($date);
    }

    $nextAvailableDate = $datesToCheck->first(function ($date) use ($doctorId, $holidayDates) {
      return !in_array($date, $holidayDates) &&
        !Appointment::where('doctor_id', $doctorId)->where('appointment_date', $date)->exists();
    });

    return response()->json([
      'status' => $nextAvailableDate ? true : false,
      'date' => $nextAvailableDate ?? 'هیچ نوبت خالی یافت نشد.'
    ]);
  }

  public function updateFirstAvailableAppointment(Request $request)
  {
    $validated = $request->validate([
      'old_date' => 'required|date', // تاریخ جدید که باید جایگزین شود
      'new_date' => 'required|date' // تاریخ جدید که باید جایگزین شود
    ]);
    $doctorId = Auth::guard('doctor')->id() ?? Auth::guard('secretary')->id();;
    try {
      // پیدا کردن اولین نوبت
      $firstAppointment = Appointment::where('doctor_id', $doctorId)
        ->orderBy('appointment_date', 'asc')
        ->first();
      if (!$firstAppointment) {
        return response()->json([
          'status' => false,
          'message' => 'هیچ نوبتی برای بروزرسانی یافت نشد.'
        ], 404);
      }
      // به‌روزرسانی تاریخ نوبت
      $firstAppointment->appointment_date = $validated['new_date'];
      $firstAppointment->save();
      return response()->json([
        'status' => true,
        'message' => 'تاریخ اولین نوبت با موفقیت بروزرسانی شد.',
        'updated_date' => $firstAppointment->appointment_date
      ]);
    } catch (\Exception $e) {
      Log::error('خطا در بروزرسانی نوبت: ' . $e->getMessage());
      return response()->json([
        'status' => false,
        'message' => 'خطا در بروزرسانی نوبت.'
      ], 500);
    }
  }
  public function getAppointmentsByDate(Request $request)
  {
    $date = $request->input('date'); // تاریخ به فرمت میلادی
    // بررسی وجود نوبت برای تاریخ مورد نظر
    $appointments = Appointment::where('appointment_date', $date)
      ->where('status', 'scheduled')
      ->get();
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
      $doctorId = Auth::guard('doctor')->id() ?? Auth::guard('secretary')->id();;
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
      Log::error('خطا در ذخیره تعطیلی: ' . $e->getMessage());
      return response()->json(['status' => false, 'message' => 'خطا در ثبت تعطیلی.'], 500);
    }
  }
  public function toggleHoliday(Request $request)
  {
    $validated = $request->validate([
      'date' => 'required|date', // تاریخ به فرمت میلادی
    ]);
    $doctorId = Auth::guard('doctor')->id() ?? Auth::guard('secretary')->id();;
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
      $doctorId = Auth::guard('doctor')->id() ?? Auth::guard('secretary')->id();;
      $holidays = DoctorHoliday::where('doctor_id', $doctorId)->get(['holiday_dates']);
      return response()->json([
        'status' => true,
        'holidays' => $holidays,
      ]);
    } catch (\Exception $e) {
      Log::error('خطا در دریافت روزهای تعطیل: ' . $e->getMessage());
      return response()->json([
        'status' => false,
        'message' => 'خطا در دریافت داده‌ها.',
      ], 500);
    }
  }
  public function destroy($id)
  {
    try {
      $appointmentSlot = AppointmentSlot::findOrFail($id);
      $appointmentSlot->delete();
      return response()->json([
        'message' => 'حذف موفقیت آمیز',
        'status' => true
      ]);
    } catch (\Exception $e) {
      return response()->json([
        'message' => 'خطا در حذف  ',
        'status' => false
      ], 500);
    }
  }
}