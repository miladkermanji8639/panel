<div class="panel panel-default">
    <div class="panel-heading">ویرایش منو</div>
    <div class="panel-body">
        <!-- نمایش پیام موفقیت -->
        @if ($successMessage)
            <div class="alert alert-success alert-dismissible fade show mt-3" role="alert">
                {{ $successMessage }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <form wire:submit.prevent="update">
            <div class="row">
                <!-- نام منو -->
                <div class="col-md-6">
                    <div class="form-group mb-3">
                        <label>نام منو</label>
                        <input type="text" class="form-control" wire:model="name">
                        @error('name') <span class="text-danger">{{ $message }}</span> @enderror
                    </div>
                </div>

                <!-- لینک منو -->
                <div class="col-md-6">
                    <div class="form-group mb-3">
                        <label>لینک منو</label>
                        <input type="text" class="form-control" wire:model="url">
                        @error('url') <span class="text-danger">{{ $message }}</span> @enderror
                    </div>
                </div>

                <!-- آیکون (آپلود فایل) -->
                <div class="col-md-6">
                    <div class="form-group mb-3">
                        <label>آیکون</label>
                        <input type="file" class="form-control" wire:model="icon">
                        @if ($currentIcon)
                            <div class="mt-2">
                                <img src="{{ Storage::url($currentIcon) }}" alt="آیکون فعلی" style="max-width: 100px;">
                                <p class="text-muted">آیکون فعلی</p>
                            </div>
                        @endif
                        @error('icon') <span class="text-danger">{{ $message }}</span> @enderror
                    </div>
                </div>

                <!-- جایگاه -->
                <div class="col-md-6">
                    <div class="form-group mb-3">
                        <label>جایگاه</label>
                        <select class="form-control" wire:model="position">
                            <option value="top">بالا</option>
                            <option value="bottom">پایین</option>
                            <option value="top_bottom">بالا و پایین</option>
                        </select>
                        @error('position') <span class="text-danger">{{ $message }}</span> @enderror
                    </div>
                </div>

                <!-- زیرمجموعه -->
                <div class="col-md-6">
                    <div class="form-group mb-3">
                        <label>زیرمجموعه</label>
                        <select class="form-control" wire:model="parent_id">
                            <option value="">[دسته اصلی]</option>
                            @foreach ($menus as $menu)
                                <option value="{{ $menu->id }}">{{ $menu->name }}</option>
                            @endforeach
                        </select>
                        @error('parent_id') <span class="text-danger">{{ $message }}</span> @enderror
                    </div>
                </div>

                <!-- ترتیب -->
                <div class="col-md-6">
                    <div class="form-group mb-3">
                        <label>ترتیب</label>
                        <input type="number" class="form-control" wire:model="order">
                        @error('order') <span class="text-danger">{{ $message }}</span> @enderror
                    </div>
                </div>

                <!-- وضعیت -->
                <div class="col-md-6">
                    <div class="form-group mb-3">
                        <label>وضعیت</label>
                        <select class="form-control" wire:model="status">
                            <option value="1">فعال</option>
                            <option value="0">غیرفعال</option>
                        </select>
                        @error('status') <span class="text-danger">{{ $message }}</span> @enderror
                    </div>
                </div>
            </div>

            <!-- دکمه‌ها -->
            <div class="d-flex justify-content-between mt-4">
                <a href="{{ route('admin.Dashboard.menu.index') }}" class="btn btn-warning">بازگشت</a>
                <button type="submit" class="btn btn-success">ویرایش</button>
            </div>
        </form>
    </div>
</div>

<script>
    document.addEventListener('livewire:load', function () {
        Livewire.on('menuUpdated', () => {
            setTimeout(() => {
                document.querySelector('.alert-success')?.remove();
            }, 5000); // حذف الرت بعد از 5 ثانیه
        });
    });
</script>