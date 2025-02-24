<?php
namespace App\Http\Middleware\Dr;

use Closure;
use Illuminate\Support\Facades\Auth;

class CheckCompleteProfile
{
  public function handle($request, Closure $next)
  {
    if ($request->expectsJson() || $request->is('livewire/*') || $request->is('admin/*') || $request->is('admin-panel/*') || $request->routeIs('dr.auth.login-register-form') || $request->routeIs('dr.auth.login-user-pass-form') || $request->routeIs('dr-two-factor') || $request->routeIs('dr-two-factor-store') || $request->routeIs('dr-login-with-mobile-pass') || $request->routeIs('dr.auth.login-register') || $request->routeIs('dr.auth.login-confirm-form') || $request->routeIs('dr.auth.login-confirm') || $request->routeIs('dr.auth.login-resend-otp')) {
      return $next($request);
    }
    $doctor = Auth::guard('doctor')->user();

    // Check if the user is authenticated and if the profile is not completed
    if (Auth::guard('doctor')->check() && !$doctor->profile_completed) {
      // Allow access to the edit profile and update profile routes
      if ($request->routeIs('dr-edit-profile') || $request->routeIs('dr-update-profile') || $request->routeIs('dr-send-mobile-otp') || $request->routeIs('dr-mobile-confirm') || $request->routeIs('dr-specialty-update') || $request->routeIs('dr-uuid-update') || $request->routeIs('dr-delete-specialty') || $request->routeIs('dr-messengers-update') || $request->routeIs('dr-static-password-update') || $request->routeIs('dr-two-factor-update') || $request->routeIs('dr-check-profile-completeness') || $request->routeIs('dr.turn.appointments.by-date')) {
        return $next($request);
      }

      // Redirect to the edit profile route
      return redirect()->route('dr-edit-profile')
        ->with('complete-profile', 'برای دسترسی به امکانات سایت لطفا ابتدا پروفایل خود را تکمیل کنید');
    }

    return $next($request);
  }
}