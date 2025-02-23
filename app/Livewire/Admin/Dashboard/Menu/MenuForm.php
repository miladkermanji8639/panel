<?php

namespace App\Livewire\Admin\Dashboard\Menu;

use Livewire\Component;
use App\Models\Menu;

class MenuForm extends Component
{
    public $menuId, $name, $url, $icon, $position = 'top', $parent_id, $order = 0, $status = 1;
    public $successMessage = '';

    protected $rules = [
        'name' => 'required|string|max:255',
        'url' => 'nullable|string|max:255',
        'icon' => 'nullable|string|max:255',
        'position' => 'required|in:top,bottom,top_bottom',
        'parent_id' => 'nullable|exists:menus,id',
        'order' => 'integer|min:0',
        'status' => 'boolean',
    ];

    public function store()
    {
        $this->validate([
            'name' => 'required|string|max:255',
            'url' => 'nullable|url',
            'icon' => 'nullable|image|max:2048', // فقط فایل‌های تصویری حداکثر ۲ مگابایت
            'position' => 'required|in:top,bottom,top_bottom',
            'parent_id' => 'nullable|exists:menus,id',
            'order' => 'nullable|integer',
            'status' => 'required|boolean',
        ]);

        if ($this->icon) {
            $iconPath = $this->icon->store('menu-icons', 'public'); // ذخیره در storage/app/public/menu-icons
        }

        Menu::create([
            'name' => $this->name,
            'url' => $this->url,
            'icon' => $iconPath ?? null,
            'position' => $this->position,
            'parent_id' => $this->parent_id,
            'order' => $this->order,
            'status' => $this->status,
        ]);

        $this->reset(['name', 'url', 'icon', 'position', 'parent_id', 'order', 'status']);
        session()->flash('success', 'منو با موفقیت اضافه شد.');

        $this->dispatch('menuAdded'); // برای نمایش اعلان موفقیت در فرانت‌اند
    }



    public function render()
    {
        return view('livewire.admin.dashboard.menu.menu-form', [
            'menus' => Menu::whereNull('parent_id')->get(),
        ]);
    }
}

