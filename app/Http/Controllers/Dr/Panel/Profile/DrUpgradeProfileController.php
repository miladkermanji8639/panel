<?php

namespace App\Http\Controllers\Dr\Panel\Profile;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use App\Models\Dr\DoctorProfileUpgrade;
use Modules\Payment\Services\PaymentService;

class DrUpgradeProfileController
{
 protected $paymentService;

 public function __construct(PaymentService $paymentService)
 {
  \Log::info('🔄 PaymentService Injected!');
  $this->paymentService = $paymentService;
 }

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
 public function payForUpgrade(Request $request)
 {
  Log::info('🚀 payForUpgrade method called!');

  // دریافت پزشک لاگین شده
  $doctor = Auth::guard('doctor')->user();
  if (!$doctor) {
   Log::error('⛔ No doctor found! User is NOT authenticated.');
   return redirect()->back()->with('error', 'ابتدا وارد حساب خود شوید.');
  }

  $amount = 780000;
  $callbackUrl = route('doctor.upgrade.callback');

  \Log::info('💰 Calling PaymentService@pay() with amount: ' . $amount);

  $paymentResponse = $this->paymentService->pay($amount, $callbackUrl, [
   'doctor_id' => $doctor->id,
   'description' => 'پرداخت برای ارتقاء حساب کاربری'
  ]);

  \Log::info('🔄 Payment Response:', ['response' => $paymentResponse]);

  // 🔍 بررسی نوع خروجی
  if ($paymentResponse instanceof \Illuminate\Http\RedirectResponse) {
   return $paymentResponse; // اگر مقدار `RedirectResponse` باشد، مستقیماً آن را بازمی‌گردانیم
  }

  if (is_string($paymentResponse)) {
   return redirect()->away($paymentResponse); // اگر مقدار `string` باشد، ریدایرکت انجام بده
  }

  return redirect()->route('doctor.upgrade')->with('error', 'خطا در انتقال به درگاه پرداخت');
 }




 /**
  * بررسی نتیجه پرداخت
  */
 public function paymentCallback()
 {
  $transaction = $this->paymentService->verify();

  if ($transaction) {
   // ثبت پرداخت موفق در جدول ارتقاء پزشکان
   DoctorProfileUpgrade::create([
    'doctor_id' => $transaction->meta['doctor_id'],
    'payment_reference' => $transaction->transaction_id,
    'payment_status' => 'paid',
    'amount' => $transaction->amount,
    'days' => 90,
    'paid_at' => now(),
    'expires_at' => now()->addDays(90),
   ]);

   return redirect()->route('doctor.upgrade')->with('success', 'پرداخت شما با موفقیت انجام شد.');
  }

  return redirect()->route('doctor.upgrade')->with('error', 'پرداخت ناموفق بود.');
 }
}