<?php
namespace App\Livewire\Dr;

use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use App\Models\Dr\DoctorPaymentSetting;

class PaymentSettingComponent extends Component
{
    public $visit_fee = 20000; // پیش‌فرض 20 هزار تومان
    public $card_number;

    public function mount()
    {
        $doctorId = Auth::guard('doctor')->user()->id;
        $settings = DoctorPaymentSetting::where('doctor_id', $doctorId)->first();

        if (!$settings) {
            DoctorPaymentSetting::create([
                'doctor_id' => $doctorId,
                'visit_fee' => $this->visit_fee,
                'card_number' => null,
            ]);
        } else {
            $this->visit_fee = $settings->visit_fee; // مقدار خام
            $this->card_number = $settings->card_number;
        }
    }

    public function render()
    {
        $doctorId = Auth::guard('doctor')->user()->id;
        $totalIncome = \App\Models\Dr\DoctorWalletTransaction::where('doctor_id', $doctorId)->sum('amount');
        $paid = \App\Models\Dr\DoctorWalletTransaction::where('doctor_id', $doctorId)->where('status', 'paid')->sum('amount');
        $available = \App\Models\Dr\DoctorWalletTransaction::where('doctor_id', $doctorId)->where('status', 'available')->sum('amount');

        return view('livewire.dr.payment-setting-component', [
            'totalIncome' => $totalIncome,
            'paid' => $paid,
            'available' => $available,
            'formatted_visit_fee' => number_format($this->visit_fee), // فرمت‌شده برای نمایش
        ]);
    }

    public function requestSettlement()
    {
        $doctorId = Auth::guard('doctor')->user()->id;
        $settings = DoctorPaymentSetting::where('doctor_id', $doctorId)->first();

        if (empty($this->card_number)) {
            $this->dispatch('toast', message: 'لطفاً شماره کارت را وارد کنید.');
            return;
        }

        // چک کردن درخواست فعال
        $existingRequest = \App\Models\Dr\DoctorSettlementRequest::where('doctor_id', $doctorId)
            ->whereIn('status', ['pending', 'approved'])
            ->exists();
        if ($existingRequest) {
            $this->dispatch('toast', message: 'شما یک درخواست تسویه فعال دارید. لطفاً منتظر پردازش باشید.');
            return;
        }

        $availableAmount = \App\Models\Dr\DoctorWalletTransaction::where('doctor_id', $doctorId)
            ->where('status', 'available')
            ->sum('amount');
        if ($availableAmount <= 0) {
            $this->dispatch('toast', message: 'مبلغ قابل برداشت وجود ندارد.');
            return;
        }

        $settings->update([
            'visit_fee' => $this->visit_fee,
            'card_number' => $this->card_number,
        ]);

        \App\Models\Dr\DoctorSettlementRequest::create([
            'doctor_id' => $doctorId,
            'amount' => $availableAmount,
            'status' => 'pending',
        ]);

        \App\Models\Dr\DoctorWalletTransaction::where('doctor_id', $doctorId)
            ->where('status', 'available')
            ->update(['status' => 'requested']);

        $this->dispatch('toast', message: 'درخواست تسویه حساب با موفقیت ثبت شد.');
    }
}