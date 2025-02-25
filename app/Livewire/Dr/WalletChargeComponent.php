<?php
namespace App\Livewire\Dr;

use Livewire\Component;
use App\Models\Dr\DoctorWallet;
use App\Models\Dr\SystemSetting;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use App\Models\Dr\DoctorWalletTransaction;
use Modules\Payment\Services\PaymentService;
use Livewire\Features\SupportRedirects\Redirector;

class WalletChargeComponent extends Component
{
    public $amount = 0;
    public $displayAmount = '';
    public $isLoading = false;
    public $transactionId;

    protected $paymentService;

    public function boot(PaymentService $paymentService)
    {
        $this->paymentService = $paymentService;
    }

    protected $rules = [
        'amount' => 'required|integer|min:1000',
    ];

    public function mount()
    {
        $this->amount = 0;
        $this->displayAmount = '';
        $this->transactionId = null;
    }

    public function render()
    {
        $doctorId = Auth::guard('doctor')->user()->id;
        $transactions = DoctorWalletTransaction::where('doctor_id', $doctorId)
            ->latest()
            ->take(10)
            ->get();
        $wallet = DoctorWallet::firstOrCreate(['doctor_id' => $doctorId], ['balance' => 0]);
        $availableAmount = $wallet->balance;

        if (session('success') && $this->transactionId) {
            $this->confirmPayment($this->transactionId);
            $this->transactionId = null;
        } elseif (session('error')) {
            $this->dispatch('toast', message: session('error'));
        }

        return view('livewire.dr.wallet-charge-component', [
            'transactions' => $transactions,
            'availableAmount' => $availableAmount,
        ]);
    }

    public function chargeWallet()
    {
        $this->validate();

        $this->isLoading = true;

        $doctorId = Auth::guard('doctor')->user()->id;
        $companyCardNumber = SystemSetting::where('key', 'company_card_number')->value('value');

        $transaction = DoctorWalletTransaction::create([
            'doctor_id' => $doctorId,
            'amount' => $this->amount,
            'status' => 'pending',
            'type' => 'charge',
            'description' => "شارژ کیف پول",
            'registered_at' => now(),
        ]);

        $this->transactionId = $transaction->id;

        $callbackUrl = route('payment.callback');
        $paymentResponse = $this->paymentService->pay($this->amount, $callbackUrl, [
            'doctor_id' => $doctorId,
            'description' => "شارژ کیف پول - تراکنش {$transaction->id}",
        ]);

        Log::info('Payment Response:', ['response' => $paymentResponse]);

        if ($paymentResponse instanceof \Illuminate\Http\RedirectResponse) {
            $this->dispatch('redirect-to-gateway', url: $paymentResponse->getTargetUrl());
        } elseif (is_string($paymentResponse)) {
            $this->dispatch('redirect-to-gateway', url: $paymentResponse);
        } elseif ($paymentResponse instanceof Redirector) {
            $this->dispatch('redirect-to-gateway', url: $paymentResponse->getIntendedUrl());
        } else {
            $this->isLoading = false;
            $this->dispatch('toast', message: 'خطا در انتقال به درگاه پرداخت');
            return;
        }

        $this->reset(['amount', 'displayAmount']);
    }

    public function confirmPayment($transactionId)
    {
        $doctorId = Auth::guard('doctor')->user()->id;
        $transaction = DoctorWalletTransaction::find($transactionId);
        if ($transaction && $transaction->status === 'pending') {
            $transaction->update(['status' => 'available']);
            $wallet = DoctorWallet::firstOrCreate(['doctor_id' => $doctorId], ['balance' => 0]);
            $wallet->increment('balance', $transaction->amount);
            $this->dispatch('toast', message: 'پرداخت با موفقیت تأیید شد.');
        }
    }

    public function deleteTransaction($transactionId)
    {
        $doctorId = Auth::guard('doctor')->user()->id;
        $transaction = DoctorWalletTransaction::where('doctor_id', $doctorId)->where('id', $transactionId)->first();

        if ($transaction) {
            $transaction->delete(); // حذف نرم
            $this->dispatch('toast', message: 'تراکنش با موفقیت حذف شد.');
        } else {
            $this->dispatch('toast', message: 'تراکنش یافت نشد!');
        }
    }

    public function updatedDisplayAmount($value)
    {
        $this->amount = $value ? (int) str_replace(',', '', $value) : 0;
    }
}