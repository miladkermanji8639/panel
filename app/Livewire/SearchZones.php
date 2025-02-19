<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Admin\Dashboard\Cities\Zone;

class SearchZones extends Component
{
    use WithPagination; // صفحه‌بندی را فعال می‌کند

    public $search = ''; // مقدار جستجو
    protected $paginationTheme = 'bootstrap'; // تنظیم صفحه‌بندی

    public function updatingSearch()
    {
        $this->resetPage(); // ریست کردن صفحه هنگام تغییر مقدار جستجو
    }

    public function render()
    {
        $cities = Zone::where('level', '1')
            ->where('name', 'like', '%' . $this->search . '%')
            ->paginate(10);

        return view('livewire.search-zones', compact('cities'));
    }
    public function searchUpdated()
    {
        $this->resetPage(); // صفحه را ریست می‌کند
    }


    public function toggleStatus($id)
    {
        $city = Zone::find($id);
        $city->status = $city->status == 1 ? 0 : 1;
        $city->save();

        // ارسال پیام برای نمایش در Toastr
        $this->dispatch('show-toastr', type: $city->status == 1 ? 'success' : 'warning', message: 'وضعیت استان با موفقیت بروزرسانی شد.');
    }



}
