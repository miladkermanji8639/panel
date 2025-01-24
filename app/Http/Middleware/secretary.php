<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class Secretary
{
    public function handle($request, Closure $next): Response
    {
        // بررسی اگر کاربر دکتر یا منشی لاگین نکرده باشد
        if (!Auth::guard('doctor')->check() && !Auth::guard('secretary')->check()) {
            return redirect()->to(route('dr.auth.login-register-form'));
        }

        // بررسی وضعیت فعال بودن برای دکتر
        if (Auth::guard('doctor')->check()) {
            $doctor = Auth::guard('doctor')->user();
            if ($doctor->status === 0) {
                return redirect()->to(route('dr.auth.login-register-form'));
            }
        }

        // بررسی وضعیت فعال بودن برای منشی
        if (Auth::guard('secretary')->check()) {
            $secretary = Auth::guard('secretary')->user();
            if ($secretary->status === 0) {
                return redirect()->to(route('dr.auth.login-register-form'));
            }
        }

        return $next($request);
    }
}
