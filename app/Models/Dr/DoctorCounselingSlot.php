<?php

namespace App\Models\Dr;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class DoctorCounselingSlot extends Model
{
     use HasFactory;
    protected $table = 'doctor_counseling_slots';

    protected $fillable = [
        'work_schedule_id',
        'time_slots',
        'max_appointments',
        'current_appointments',
        'is_active',
        'is_booked',
        'slot_date',
    ];

    protected $casts = [
        'time_slots' => 'array',
        'is_active' => 'boolean',
        'is_booked' => 'boolean',
    ];

    public function workSchedule()
    {
        return $this->belongsTo(DoctorCounselingWorkSchedule::class, 'work_schedule_id');
    }
}
