<?php

namespace App\Http\Middleware\Dr;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CheckSecretaryPermission
{
    public function handle(Request $request, Closure $next, $permission = null)
    {
        $user = Auth::guard('secretary')->user();

        // اگر کاربر پزشک باشد، اجازه دسترسی دارد
        if (Auth::guard('doctor')->check()) {
            return $next($request);
        }

        // بررسی دسترسی‌های منشی
        if ($user && in_array($permission, json_decode($user->permissions->permissions ?? '[]', true))) {
            return $next($request);
        }

        // اگر دسترسی نداشت
        return abort(403, 'شما اجازه‌ی دسترسی به این بخش را ندارید.');
    }
}
