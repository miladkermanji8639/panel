<?php

namespace App\Http\Controllers\Dr\Panel\Profile;

use Illuminate\Support\Facades\Auth;
use App\Models\Dr\DoctorProfileUpgrade;

class DrUpgradeProfileController
{
 public function index()
 {
  // دریافت پزشک لاگین شده
  $doctor = Auth::guard('doctor')->user();

  if (!$doctor) {
   abort(403, 'شما به این بخش دسترسی ندارید.');
  }

  // دریافت لیست پرداخت‌های پزشک
  $payments = DoctorProfileUpgrade::where('doctor_id', $doctor->id)
   ->orderBy('created_at', 'desc')
   ->paginate(10); // صفحه‌بندی برای نمایش لیست

  return view('dr.panel.profile.upgrade', compact('payments'));
 }
 public function deletePayment($id)
 {
  $payment = DoctorProfileUpgrade::find($id);

  if (!$payment) {
   return response()->json(['success' => false, 'message' => 'پرداخت یافت نشد!'], 404);
  }

  $payment->delete();

  return response()->json(['success' => true, 'message' => 'پرداخت با موفقیت حذف شد.']);
 }

}