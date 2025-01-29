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
  $secretaries = $doctor->secretaries; // دریافت لیست منشی‌ها
  $permissions = config('permissions'); // دریافت لیست بخش‌های پنل از کانفیگ
  return view('dr.panel.secretary_permissions.index', compact('secretaries', 'permissions'));
 }

 public function update(Request $request, $secretaryId)
 {
  $doctor = auth()->guard('doctor')->user();

  $request->validate([
   'permissions' => 'array', // مقدار باید یک آرایه باشد
  ]);

  // ذخیره اطلاعات
  SecretaryPermission::updateOrCreate(
   ['doctor_id' => $doctor->id, 'secretary_id' => $secretaryId],
   ['permissions' => $request->permissions]
  );

  return response()->json(['success' => true, 'message' => 'دسترسی‌ها با موفقیت ذخیره شد.']);
 }
}
