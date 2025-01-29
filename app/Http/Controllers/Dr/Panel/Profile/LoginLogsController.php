<?php

namespace App\Http\Controllers\Dr\Panel\Profile;

use App\Models\LoginLog;
use Illuminate\Support\Facades\Auth;

class LoginLogsController{
 public function security()
 {
  $doctor = Auth::guard('doctor')->check() ? Auth::guard('doctor')->user() : null;

  if (!$doctor) {
   return redirect()->route('dr.auth.login-register-form')->with('error', 'ابتدا وارد شوید.');
  }

  // دریافت لاگ‌های دکتر
  $doctorLogs = LoginLog::where('doctor_id', $doctor->id)->orderBy('login_at', 'desc')->paginate(5);

  // دریافت لاگ‌های منشی‌های دکتر
  $secretaryIds = $doctor->secretaries ? $doctor->secretaries->pluck('id')->toArray() : [];
  $secretaryLogs = LoginLog::whereIn('secretary_id', $secretaryIds)->orderBy('login_at', 'desc')->paginate(5);

  return view("dr.panel.profile.security", compact('doctorLogs', 'secretaryLogs'));
 }


 public function deleteLog($id)
 {
  $log = LoginLog::find($id);

  if (!$log) {
   return response()->json(['success' => false, 'message' => 'لاگ یافت نشد'], 404);
  }

  $log->delete();

  return response()->json(['success' => true, 'message' => 'لاگ با موفقیت حذف شد']);
 }
}