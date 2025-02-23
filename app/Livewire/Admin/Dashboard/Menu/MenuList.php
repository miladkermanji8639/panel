<?php

namespace App\Livewire\Admin\Dashboard\Menu;

use Livewire\Component;
use App\Models\Menu;
use Livewire\WithPagination;

class MenuList extends Component
{
    use WithPagination;

    public $search = '';
    public $selectedRows = [];
    public $selectAll = false;

    protected $listeners = ['updateDeleteButton' => 'checkIfRowsSelected', 'deleteSelectedMenus' => 'deleteSelected'];
    


    public function updatedSelectAll($value)
    {
        if ($value) {
            $this->selectedRows = Menu::pluck('id')->toArray();
        } else {
            $this->selectedRows = [];
        }
    }

    public function checkIfRowsSelected()
    {
        $this->selectAll = count($this->selectedRows) === Menu::count();
    }

    public function deleteSelected()
    {
        if (count($this->selectedRows) > 0) {
            Menu::whereIn('id', $this->selectedRows)->delete();
            $this->selectedRows = [];
            $this->selectAll = false;
            $this->dispatch('refreshDeleteButton', hasSelectedRows: false);
            $this->dispatch('show-toastr', type: 'success', message: 'منو های انتخاب‌شده با موفقیت حذف شدند.');
        }
    }
    public function toggleStatus($id)
    {
        $city = Menu::find($id);
        $city->status = !$city->status;
        $city->save();

        $this->dispatch('show-toastr', type: 'success', message: 'وضعیت منو با موفقیت تغییر کرد.');
    }





    public function searchUpdated()
    {
        $this->resetPage();
    }

    public function render()
    {
        return view('livewire.admin.dashboard.menu.menu-list', [
            'menus' => Menu::where('name', 'like', "%{$this->search}%")->paginate(10),
        ]);
    }
   
}
