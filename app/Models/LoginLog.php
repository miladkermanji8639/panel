<?php

namespace App\Models;

use App\Models\User;
use App\Models\Dr\Doctor;
use App\Models\Dr\Secretary;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class LoginLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'doctor_id',
        'secretary_id',
        'user_type',
        'login_at',
        'logout_at',
        'ip_address',
        'device'
    ];

    public function doctor()
    {
        return $this->belongsTo(Doctor::class);
    }

    public function secretary()
    {
        return $this->belongsTo(Secretary::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
