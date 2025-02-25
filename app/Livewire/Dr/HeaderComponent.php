<?php
namespace App\Livewire\Dr;

use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use App\Models\Dr\DoctorWalletTransaction;

class HeaderComponent extends Component
{
    public $walletBalance = 0;

    public function mount()
    {
        $doctorId = Auth::guard('doctor')->user()->id;
        $this->walletBalance = DoctorWalletTransaction::where('doctor_id', $doctorId)
            ->where('status', 'available')
            ->sum('amount');
    }

    public function render()
    {
        return view('livewire.dr.header-component');
    }
}