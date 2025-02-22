<div>
    <div class="card-header d-flex justify-content-between">
        <div class="d-flex align-items-center">
            <input type="search" class="form-control  w-100 me-2" placeholder="جستجو تخصص" wire:model="search"
                wire:keyup="searchUpdated">
        </div>
    <button class="btn btn-danger" wire:click="confirmDelete" wire:loading.attr="disabled" id="deleteButton"
        x-bind:disabled="<?php echo e(count($selectedRows) === 0); ?>">
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
                <!--[if BLOCK]><![endif]--><?php $__currentLoopData = $specialties; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $specialty): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <tr>
                        <td>
                            <input type="checkbox" class="form-check-input" wire:model="selectedRows"
                                value="<?php echo e($specialty->id); ?>" x-on:change="$wire.dispatch('updateDeleteButton')">
                        </td>
                        <td><?php echo e($specialty->id); ?></td>
                        <td><?php echo e($specialty->name); ?></td>
                        <td>
                            <span wire:click="toggleStatus(<?php echo e($specialty->id); ?>)"
                                class="badge bg-label-<?php echo e($specialty->status == 1 ? 'success' : 'danger'); ?> cursor-pointer">
                                <?php echo e($specialty->status == 1 ? 'فعال' : 'غیر فعال'); ?>

                            </span>
                        </td>
                    <td>
                        <div class="dropdown">
                            <button class="btn dropdown-toggle hide-arrow p-0" data-bs-toggle="dropdown" type="button">
                                <i class="ti ti-dots-vertical"></i>
                            </button>
                            <div class="dropdown-menu">
                             
                                <a class="dropdown-item" href="<?php echo e(url('admin/dashboard/specialty/edit/' . $specialty->id)); ?>">
                                    <i class="ti ti-pencil me-1"></i> ویرایش
                                </a>
                                <form method="POST" action="<?php echo e(url('admin/dashboard/specialty/delete/' . $specialty->id)); ?>">
                                    <?php echo csrf_field(); ?>
                                    <?php echo method_field('DELETE'); ?>
                                    <button type="submit" class="dropdown-item delete">
                                        <i class="ti ti-trash me-1"></i> حذف
                                    </button>
                                </form>
                            </div>
                        </div>
                    </td>

                    </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><!--[if ENDBLOCK]><![endif]-->
            </tbody>
        </table>
    </div>

    <div class="row mx-2 mt-4">
        <div class="col-sm-12 col-md-6">
            <?php echo e($specialties->links('pagination::bootstrap-5')); ?>

        </div>
    </div>
</div><?php /**PATH D:\MyProjects\Benobe\panel\resources\views/livewire/search-specialties.blade.php ENDPATH**/ ?>