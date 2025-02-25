<?php
namespace App\Livewire\Dr;

use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use App\Models\Dr\DoctorWallet;
use App\Models\Dr\DoctorWalletTransaction;

class WalletComponent extends Component
{
    public $availableAmount = 0;
    public $transactions = [];

    public function mount()
    {
        $this->loadData();
    }

    public function render()
    {
        $this->loadData();
        return view('livewire.dr.wallet-component');
    }

    public function requestSettlement()
    {
        $doctorId = Auth::guard('doctor')->user()->id;
        $wallet = DoctorWallet::where('doctor_id', $doctorId)->first();

        if ($wallet && $wallet->balance > 0) {
            DoctorWalletTransaction::create([
                'doctor_id' => $doctorId,
                'amount' => -$wallet->balance, // برداشت کل موجودی
                'status' => 'requested',
                'type' => 'withdraw',
                'description' => 'درخواست آزادسازی موجودی',
                'registered_at' => now(),
            ]);
            $wallet->decrement('balance', $wallet->balance);
            $this->dispatch('toast', message: 'درخواست آزادسازی با موفقیت ثبت شد.');
        } else {
            $this->dispatch('toast', message: 'مبلغ قابل برداشت وجود ندارد!');
        }

        $this->loadData();
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

        $this->loadData();
    }

    protected function loadData()
    {
        $doctorId = Auth::guard('doctor')->user()->id;
        $this->transactions = DoctorWalletTransaction::where('doctor_id', $doctorId)
            ->latest()
            ->take(10)
            ->get();
        $wallet = DoctorWallet::where('doctor_id', $doctorId)->firstOrCreate(['doctor_id' => $doctorId], ['balance' => 0]);
        $this->availableAmount = $wallet->balance;
    }
}