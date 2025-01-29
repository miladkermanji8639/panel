<?php

namespace App\Http\Controllers\Dr\Panel\SecretaryPermission;

use App\Models\Dr\Doctor;
use App\Models\Dr\Secretary;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Dr\SecretaryPermission;

class SecretaryPermissionController
{
 public function index()
 {
  $doctor = auth()->guard('doctor')->user();

  if (!$doctor) {
   return redirect()->route('dr.auth.login-register-form');
  }

  $secretaries = $doctor->secretaries()->with('permissions')->get(); // دریافت منشی‌ها همراه با دسترسی‌ها
  $permissions = config('permissions'); // لیست دسترسی‌های قابل انتخاب

  return view('dr.panel.secretary_permissions.index', compact('secretaries', 'permissions'));
 }

 public function update(Request $request, $secretaryId)
 {
  $doctor = auth()->guard('doctor')->user();

  if (!$doctor) {
   return response()->json([
    'success' => false,
    'message' => 'شما اجازه‌ی این عملیات را ندارید.'
   ], 403);
  }

  $request->validate([
   'permissions' => 'array'
  ]);

  SecretaryPermission::updateOrCreate(
   ['doctor_id' => $doctor->id, 'secretary_id' => $secretaryId],
   ['permissions' => json_encode($request->permissions)]
  );

  return response()->json([
   'success' => true,
   'message' => 'دسترسی‌های منشی با موفقیت ذخیره شد.'
  ]);
 }
}