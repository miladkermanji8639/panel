<div>
    <div class="card-header d-flex justify-content-between flex-wrap gap-20">
        <div class="d-flex align-items-center">
            <input type="search" class="form-control w-100 me-2" placeholder="جستجو تعرفه" wire:model="search" wire:keyup="searchUpdated">
        </div>
        <a href="<?php echo e(route('admin.Dashboard.membershipfee.create')); ?>" class="btn btn-primary">
            <i class="ti ti-plus"></i> افزودن تعرفه جدید
        </a>
        <button class="btn btn-danger" wire:click="confirmDelete" wire:loading.attr="disabled" id="deleteButton" 
                <?php echo e(!$hasSelectedRows ? 'disabled' : ''); ?>>
            <i class="ti ti-trash"></i> حذف انتخاب‌شده‌ها
        </button>
    </div>

    <div class="table-responsive text-nowrap">
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>
                        <input type="checkbox" class="form-check-input" wire:model.live="selectAll">
                    </th>
                    <th>نام</th>
                    <th>روز</th>
                    <th>قیمت</th>
                    <th>وضعیت</th>
                    <th>عملیات</th>
                </tr>
            </thead>
            <tbody>
                <!--[if BLOCK]><![endif]--><?php $__empty_1 = true; $__currentLoopData = $fees; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $fee): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <tr>
                        <td>
                            <input type="checkbox" class="form-check-input" wire:model.live="selectedRows" value="<?php echo e($fee->id); ?>">
                        </td>
                        <td><?php echo e($fee->name); ?></td>
                        <td><?php echo e($fee->days); ?> روز</td>
                        <td><?php echo e(number_format($fee->price)); ?> تومان</td>
                        <td>
                            <span wire:click="toggleStatus(<?php echo e($fee->id); ?>)"
                                  class="badge bg-label-<?php echo e($fee->status ? 'success' : 'danger'); ?> cursor-pointer">
                                <?php echo e($fee->status ? 'فعال' : 'غیرفعال'); ?>

                            </span>
                        </td>
                        <td>
                            <div class="dropdown">
                                <button class="btn dropdown-toggle hide-arrow p-0" data-bs-toggle="dropdown">
                                    <i class="ti ti-dots-vertical"></i>
                                </button>
                                <div class="dropdown-menu">
                                    <a class="dropdown-item" href="<?php echo e(route('admin.Dashboard.membershipfee.edit', $fee->id)); ?>">
                                        <i class="ti ti-pencil me-1"></i> ویرایش
                                    </a>
                                </div>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <tr>
                        <td colspan="6" class="text-center">هیچ تعرفه‌ای یافت نشد.</td>
                    </tr>
                <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
            </tbody>
        </table>
    </div>

    <div class="row mx-2 mt-4">
        <div class="col-sm-12 col-md-6">
            <?php echo e($fees->links('pagination::bootstrap-5')); ?>

        </div>
    </div>
</div>

<!-- اسکریپت تأیید حذف و اعلان‌ها -->
<script>
document.addEventListener('livewire:init', () => {
    Livewire.on('show-delete-confirmation', () => {
        Swal.fire({
            title: 'آیا مطمئن هستید؟',
            text: 'این عمل قابل بازگشت نیست!',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'بله، حذف کن!',
            cancelButtonText: 'خیر',
        }).then((result) => {
            if (result.isConfirmed) {
                Livewire.dispatch('doDeleteSelected');
            }
        });
    });


});
</script><?php /**PATH D:\MyProjects\Benobe\panel\resources\views/livewire/admin/dashboard/membershipfee/membership-fee-component.blade.php ENDPATH**/ ?>