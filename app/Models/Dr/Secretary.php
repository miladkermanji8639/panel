<?php

namespace App\Models\Dr;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Secretary extends Model
{
    use HasFactory;

    protected $fillable = [
        'doctor_id',
        'first_name',
        'last_name',
        'email',
        'phone',
        'national_code',
        'gender',
        'address',
        'birth_date',
        'profile_photo_path',
        'password',
        'is_active',
    ];

    // ارتباط با مدل دکتر
    public function doctor()
    {
        return $this->belongsTo(Doctor::class);
    }
}
