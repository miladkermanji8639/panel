<?php
namespace App\Livewire\Dr;

use Livewire\Component;
use App\Models\Dr\DoctorWalletTransaction;
use Illuminate\Support\Facades\Auth;

class WalletComponent extends Component
{
    public function render()
    {
        $doctorId = Auth::guard('doctor')->user()->id;
        $transactions = DoctorWalletTransaction::where('doctor_id', $doctorId)->get();
        $availableAmount = DoctorWalletTransaction::where('doctor_id', $doctorId)->where('status', 'available')->sum('amount');

        return view('livewire.dr.wallet-component', [
            'transactions' => $transactions,
            'availableAmount' => $availableAmount,
        ]);
    }

    public function requestSettlement()
    {
        $doctorId = Auth::guard('doctor')->user()->id;

        // چک کردن درخواست فعال
        $existingRequest = \App\Models\Dr\DoctorSettlementRequest::where('doctor_id', $doctorId)
            ->whereIn('status', ['pending', 'approved'])
            ->exists();
        if ($existingRequest) {
            $this->dispatch('toast', message: 'شما یک درخواست تسویه فعال دارید. لطفاً منتظر پردازش باشید.');
            return;
        }

        $availableAmount = DoctorWalletTransaction::where('doctor_id', $doctorId)
            ->where('status', 'available')
            ->sum('amount');
        if ($availableAmount <= 0) {
            $this->dispatch('toast', message: 'مبلغ قابل برداشت وجود ندارد.');
            return;
        }

        \App\Models\Dr\DoctorSettlementRequest::create([
            'doctor_id' => $doctorId,
            'amount' => $availableAmount,
            'status' => 'pending',
        ]);

        DoctorWalletTransaction::where('doctor_id', $doctorId)
            ->where('status', 'available')
            ->update(['status' => 'requested']);

        $this->dispatch('toast', message: 'درخواست آزادسازی با موفقیت ثبت شد.');
    }
}