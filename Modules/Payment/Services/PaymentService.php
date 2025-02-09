<?php

namespace Modules\Payment\Services;

use Shetabit\Multipay\Invoice;
use Shetabit\Payment\Facade\Payment;
use Modules\Payment\App\Http\Models\Transaction;
use Illuminate\Http\RedirectResponse;

class PaymentService
{
  /**
   * ایجاد پرداخت و هدایت کاربر به درگاه
   *
   * @param float $amount مبلغ پرداختی
   * @param string|null $callbackUrl آدرس بازگشت
   * @param array $meta اطلاعات اضافی برای ذخیره در تراکنش
   * @return RedirectResponse
   */
  public function pay($amount, $callbackUrl = null, $meta = [])
  {


    $gateway = config('payment.default_gateway');

    $callbackUrl = $callbackUrl ?? route('payment.callback');

    // ایجاد تراکنش در دیتابیس
    $transaction = Transaction::create([
      'user_id' => auth()->id(),
      'amount' => $amount,
      'gateway' => $gateway,
      'status' => 'pending',
      'meta' => $meta,
    ]);

    // ایجاد فاکتور پرداخت
    $invoice = new Invoice;
    $invoice->amount($amount);

    // اجرای پرداخت
    $redirection = Payment::via($gateway)
      ->callbackUrl($callbackUrl)
      ->purchase(
        $invoice,
        function ($driver, $transactionId) use ($transaction) {
          $transaction->update(['transaction_id' => $transactionId]);
        }
      )->pay();

    // بررسی اگر `RedirectResponse` باشد
    if ($redirection instanceof RedirectResponse) {
      return $redirection;
    }

    // بررسی اگر `RedirectionForm` باشد
    if (method_exists($redirection, 'getAction')) {
      return redirect()->away($redirection->getAction());
    }

    // بررسی اگر مقدار `string URL` باشد
    if (is_string($redirection)) {
      return redirect()->away($redirection);
    }

    // اگر هیچ مقدار معتبری نبود
    return redirect()->route('doctor.upgrade')->with('error', 'خطا در انتقال به درگاه پرداخت');
  }

  public function verify()
  {
    try {
      $receipt = Payment::verify(); // تأیید پرداخت از طریق درگاه
      $transactionId = $receipt->getReferenceId();

      // پیدا کردن تراکنش مرتبط در دیتابیس
      $transaction = Transaction::where('transaction_id', $transactionId)->first();

      if ($transaction) {
        $transaction->update(['status' => 'paid']);
        return $transaction;
      }
    } catch (\Exception $e) {
      return false;
    }

    return false;
  }


}
