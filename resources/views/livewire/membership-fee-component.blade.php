<div>
    <div class="card-header d-flex justify-content-between">
        
        <div class="d-flex align-items-center">
        <input type="search" class="form-control  w-100 me-2" placeholder="جستجو شهر" wire:model="search"
            wire:keyup="searchUpdated">
        </div>
        <a href="{{ route('admin.Dashboard.membershipfee.create') }}" class="btn btn-primary">
            <i class="ti ti-plus"></i> افزودن تعرفه جدید
        </a>
        <button class="btn btn-danger" wire:click="confirmDelete" wire:loading.attr="disabled" id="deleteButton"
            x-bind:disabled="{{ count($selectedRows) === 0 }}">
            <i class="ti ti-trash"></i> حذف انتخاب‌شده‌ها
        </button>
    </div>

    <div class="table-responsive text-nowrap">
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>
                        <input type="checkbox" class="form-check-input" wire:model="selectAll"
                            x-on:change="$wire.dispatch('updateDeleteButton')">
                    </th>
                    <th>نام</th>
                    <th>روز</th>
                    <th>قیمت</th>
                    <th>وضعیت</th>
                    <th>عملیات</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($fees as $fee)
                    <tr>
                        <td><input type="checkbox" class="form-check-input" wire:model="selectedRows" value="{{ $fee->id }}"
                            x-on:change="$wire.dispatch('updateDeleteButton')"></td>
                        <td>{{ $fee->name }}</td>
                        <td>{{ $fee->days }} روز</td>
                        <td>{{ number_format($fee->price) }} تومان</td>
                        <td>
                            <span wire:click="toggleStatus({{ $fee->id }})"
                                class="badge bg-label-{{ $fee->status ? 'success' : 'danger' }} cursor-pointer">
                                {{ $fee->status ? 'فعال' : 'غیرفعال' }}
                            </span>
                        </td>
                        <td>
                            <div class="dropdown">
                                <button class="btn dropdown-toggle hide-arrow p-0" data-bs-toggle="dropdown">
                                    <i class="ti ti-dots-vertical"></i>
                                </button>
                                <div class="dropdown-menu">
                                    <a class="dropdown-item"
                                        href="{{ route('admin.Dashboard.membershipfee.edit', $fee->id) }}">
                                        <i class="ti ti-pencil me-1"></i> ویرایش
                                    </a>
                                    <button type="button" class="dropdown-item delete" wire:click="confirmDelete">
                                        <i class="ti ti-trash me-1"></i> حذف
                                    </button>
                                </div>
                            </div>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="row mx-2 mt-4">
        <div class="col-sm-12 col-md-6">
            {{ $fees->links('pagination::bootstrap-5') }}
        </div>
    </div>
</div>