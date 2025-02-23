<div>
    <div class="card-header d-flex justify-content-between">
        <div class="d-flex align-items-center">
            <input type="search" class="form-control  w-100 me-2" placeholder="جستجو تخصص" wire:model="search"
                wire:keyup="searchUpdated">
        </div>
            <a href="{{ route('admin.Dashboard.specialty.create') }}" class="btn btn-primary">
                <i class="ti ti-plus"></i> افزودن تخصص جدید
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
                    <th>کد تخصص</th>
                    <th>نام تخصص</th>
                    <th>وضعیت</th>
                    <th>عملیات</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($specialties as $specialty)
                    <tr>
                        <td>
                            <input type="checkbox" class="form-check-input" wire:model="selectedRows"
                                value="{{ $specialty->id }}" x-on:change="$wire.dispatch('updateDeleteButton')">
                        </td>
                        <td>{{ $specialty->id }}</td>
                        <td>{{ $specialty->name }}</td>
                        <td>
                            <span wire:click="toggleStatus({{ $specialty->id }})"
                                class="badge bg-label-{{ $specialty->status == 1 ? 'success' : 'danger' }} cursor-pointer">
                                {{ $specialty->status == 1 ? 'فعال' : 'غیر فعال' }}
                            </span>
                        </td>
                    <td>
                        <div class="dropdown">
                            <button class="btn dropdown-toggle hide-arrow p-0" data-bs-toggle="dropdown" type="button">
                                <i class="ti ti-dots-vertical"></i>
                            </button>
                            <div class="dropdown-menu">
                             {{--    @if ($specialty->level == 1)
                                    <a class="dropdown-item" href="{{ url('admin/dashboard/specialty/show/' . $specialty->id) }}">
                                        <i class="ti ti-eye me-1"></i> مشاهده زیردسته
                                    </a>
                                @endif --}}
                                <a class="dropdown-item" href="{{ url('admin/dashboard/specialty/edit/' . $specialty->id) }}">
                                    <i class="ti ti-pencil me-1"></i> ویرایش
                                </a>
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
            {{ $specialties->links('pagination::bootstrap-5') }}
        </div>
    </div>
</div>