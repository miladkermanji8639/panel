<?php

namespace App\Livewire\Dr\Panel\DoctorServices;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Dr\DoctorService;

class DoctorServices extends Component
{
    use WithPagination;

    public $search = '';
    public $selectedRows = [];
    public $selectAll = false;

    public $editingService = null;
    public $name;
    public $doctor_id;
    public $duration;
    public $price;
    public $discount;
    public $status;
    public $parent_id;
    public $description;

    protected $paginationTheme = 'bootstrap';
    protected $listeners = [
        'updateDeleteButton' => 'refreshDeleteButton',
        'doDelete' => 'delete'
    ];

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function toggleStatus($id)
    {
        $service = DoctorService::find($id);
        if ($service) {
            // فرض بر این است که وضعیت به صورت عددی ذخیره شده است: 1=فعال، 0=غیرفعال
            $service->status = $service->status == 1 ? 0 : 1;
            $service->save();
            $this->dispatch('show-toastr', type: 'success', message: 'وضعیت خدمت با موفقیت تغییر کرد.');

        }
    }

    public function edit($id)
    {
        $service = DoctorService::find($id);
        if ($service) {
            $this->editingService = $service->id;
            $this->name        = $service->name;
            $this->doctor_id   = $service->doctor_id;
            $this->duration    = $service->duration;
            $this->price       = $service->price;
            $this->discount    = $service->discount;
            $this->status      = $service->status;
            $this->parent_id   = $service->parent_id;
            $this->description = $service->description;
            $this->dispatch('show-edit-modal');
        }
    }

    public function updateService()
    {
        $this->validate([
            'doctor_id'   => 'required|exists:doctors,id',
            'name'        => 'required|string|max:255',
            'duration'    => 'required|integer',
            'price'       => 'required|numeric',
            'discount'    => 'nullable|numeric',
            'status'      => 'required|in:0,1',
            'parent_id'   => 'nullable|exists:doctor_services,id',
            'description' => 'nullable|string',
        ]);

        if ($this->editingService) {
            $service = DoctorService::find($this->editingService);
            if ($service) {
                $service->update([
                    'doctor_id'   => $this->doctor_id,
                    'name'        => $this->name,
                    'duration'    => $this->duration,
                    'price'       => $this->price,
                    'discount'    => $this->discount,
                    'status'      => $this->status,
                    'parent_id'   => $this->parent_id,
                    'description' => $this->description,
                ]);
                $this->dispatch('show-toastr', [
                    'type' => 'success',
                    'message' => 'خدمت با موفقیت به‌روزرسانی شد.'
                ]);
                $this->resetInputFields();
                $this->dispatch('hide-edit-modal');
            }
        }
    }

    public function resetInputFields()
    {
        $this->editingService = null;
        $this->name = '';
        $this->doctor_id = '';
        $this->duration = '';
        $this->price = '';
        $this->discount = '';
        $this->status = '';
        $this->parent_id = '';
        $this->description = '';
    }

    public function confirmDelete($id)
    {
        $this->dispatch('show-delete-confirmation', ['id' => $id]);
    }

    public function delete($id)
    {
        DoctorService::destroy($id);
        $this->dispatch('show-toastr', [
            'type' => 'success',
            'message' => 'خدمت با موفقیت حذف شد.'
        ]);
    }

    public function render()
    {
        $services = DoctorService::whereNull('parent_id')->with('children')->get();
        return view('livewire.dr.panel.doctor-services.doctor-services', compact('services'));
    }



}
