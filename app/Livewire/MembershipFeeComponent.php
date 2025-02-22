<?php
namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Admin\MembershipFee;

class MembershipFeeComponent extends Component
{
    use WithPagination;

    public $search = '';
    public $selectedRows = [];
    public $selectAll = false;

    protected $paginationTheme = 'bootstrap';
    protected $listeners = ['updateDeleteButton' => 'refreshDeleteButton', 'doDeleteSelected' => 'deleteSelected'];

    // **جستجو هنگام تایپ**
    public function updatingSearch()
    {
        $this->resetPage();
    }

    // **انتخاب همه**
    public function updatedSelectAll($value)
    {
        $this->selectedRows = $value ? MembershipFee::pluck('id')->toArray() : [];
        $this->dispatch('refreshDeleteButton', hasSelectedRows: count($this->selectedRows) > 0);
    }

    // **تغییر وضعیت**
    public function toggleStatus($id)
    {
        $fee = MembershipFee::find($id);
        $fee->status = !$fee->status;
        $fee->save();
        $this->dispatch('show-toastr', type: 'success', message: 'وضعیت تعرفه تغییر کرد.');
    }

    // **تأیید حذف**
    public function confirmDelete()
    {
        if (count($this->selectedRows) > 0) {
            $this->dispatch('show-delete-confirmation');
        }
    }

    // **حذف انتخاب‌شده‌ها**
    public function deleteSelected()
    {
        if (count($this->selectedRows) > 0) {
            MembershipFee::whereIn('id', $this->selectedRows)->delete();
            $this->selectedRows = [];
            $this->selectAll = false;
            $this->dispatch('refreshDeleteButton', hasSelectedRows: false);
            $this->dispatch('show-toastr', type: 'success', message: 'تعرفه‌های انتخاب‌شده حذف شدند.');
        }
    }

    public function render()
    {
        $fees = MembershipFee::where('name', 'like', '%' . $this->search . '%')->where('user_type','doctor')->paginate(10);
        return view('livewire.membership-fee-component', compact('fees'));
    }
    public function searchUpdated()
    {
        $this->resetPage(); // هنگام تغییر مقدار جستجو، صفحه را ریست می‌کند
    }
}
