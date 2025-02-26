<?php
namespace App\Livewire\Dr;

use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class DrSidebar extends Component
{
    public $specialtyName;

    public function mount()
    {
        $this->specialtyName = optional(Auth::guard('doctor')->user())->specialty?->title ?? 'نامشخص';
    }

    public function render()
    {
        $user = Auth::guard('doctor')->check() ? Auth::guard('doctor')->user() : Auth::guard('secretary')->user();
        Log::info('Rendering sidebar', ['user_id' => $user->id, 'photo_path' => $user->profile_photo_path]);
        return view('livewire.dr.dr-sidebar', compact('user'));
    }
}