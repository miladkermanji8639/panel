<?php

namespace App\Models\Dr;

use Illuminate\Database\Eloquent\Model;

class DoctorWorkSchedule extends Model
{


    protected $fillable = [
        'doctor_id',
        'day',
        'is_working', // اضافه کردن is_working به فیلدهای قابل پر شدن
        'work_hours',
        'appointment_settings'
    ];

    protected $casts = [
        'work_hours' => 'array',
        'appointment_settings' => 'array',
        'is_working' => 'boolean'
    ];

    public function doctor()
    {
        return $this->belongsTo(Doctor::class);
    }

    public function slots()
    {
        return $this->hasMany(AppointmentSlot::class, 'work_schedule_id');
    }
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

    private function calculateMaxAppointments($startTime, $endTime)
    {
        // محاسبه تعداد نوبت‌ها بر اساس زمان شروع و پایان
        $start = \Carbon\Carbon::createFromFormat('H:i', $startTime);
        $end = \Carbon\Carbon::createFromFormat('H:i', $endTime);

        // محاسبه تفاوت زمانی به دقیقه
        $diffInMinutes = $start->diffInMinutes($end);

        // فرض کنید هر نوبت 20 دقیقه طول می‌کشد
        return floor($diffInMinutes / 20);
    }
}
