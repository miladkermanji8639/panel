<div>
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <div>
                <input type="search" class="form-control w-100 me-2" placeholder="جستجو پزشک" wire:model="search"
                    wire:keyup="searchUpdated">
            </div>
            <div>
                <button class="btn btn-danger" id="deleteButton" <?php if(empty($selectedRows)): echo 'disabled'; endif; ?> onclick="confirmDelete()">
                    <i class="ti ti-trash"></i> حذف انتخاب‌شده‌ها
                </button>
                <a href="<?php echo e(route('admin.Dashboard.home_page.create')); ?>" class="btn btn-primary">
                    <i class="ti ti-plus"></i> افزودن پزشک برتر
                </a>
            </div>
        </div>

        <div class="table-responsive">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>
                            <input type="checkbox" class="form-check-input" wire:model="selectAll"
                                x-on:change="$wire.dispatch('updateDeleteButton')">
                        </th>
                        <th>نام پزشک</th>
                        <th>مرکز درمان</th>
                        <th>پزشک برتر</th>
                        <th>مشاور برتر</th>
                        <th>وضعیت</th>
                        <th>عملیات</th>
                    </tr>
                </thead>
                <tbody>
                    <!--[if BLOCK]><![endif]--><?php $__currentLoopData = $bestDoctors; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $doctor): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <tr>
                            <td>
                                <input type="checkbox" class="form-check-input" wire:model="selectedRows"
                                    value="<?php echo e($doctor->id); ?>" x-on:change="$wire.dispatch('updateDeleteButton')">
                            </td>
                            <td><?php echo e($doctor->doctor->first_name . ' ' . $doctor->doctor->last_name); ?></td>
                            <td><?php echo e($doctor->hospital->name ?? '---'); ?></td>
                            <td>
                                <?php echo $doctor->best_doctor ? '<i class="fa fa-check-circle" style="color:green"></i>' : '---'; ?>

                            </td>
                            <td>
                                <?php echo $doctor->best_consultant ? '<i class="fa fa-check-circle" style="color:green"></i>' : '---'; ?>

                            </td>
                            <td>
                                <span wire:click="toggleStatus(<?php echo e($doctor->id); ?>)"
                                    class="badge bg-label-<?php echo e($doctor->status ? 'success' : 'danger'); ?> cursor-pointer">
                                    <?php echo e($doctor->status ? 'فعال' : 'غیرفعال'); ?>

                                </span>
                            </td>
                            <td>
                                <div class="dropdown">
                                    <button class="btn p-0" data-bs-toggle="dropdown">
                                        <i class="ti ti-dots-vertical"></i>
                                    </button>
                                    <div class="dropdown-menu">
                                        <a class="dropdown-item"
                                            href="<?php echo e(route('admin.Dashboard.home_page.edit', $doctor->id)); ?>">
                                            <i class="ti ti-pencil"></i> ویرایش
                                        </a>
                                        <button class="dropdown-item text-danger"
                                            wire:click="deleteSelected(<?php echo e($doctor->id); ?>)">
                                            <i class="ti ti-trash"></i> حذف
                                        </button>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><!--[if ENDBLOCK]><![endif]-->
                </tbody>
            </table>
        </div>

        <div class="d-flex justify-content-between mt-3 p-3">
            <span>نمایش <?php echo e($bestDoctors->count()); ?> از <?php echo e($bestDoctors->total()); ?> پزشک</span>
            <?php echo e($bestDoctors->links()); ?>

        </div>
    </div>
</div>
<script>
    document.addEventListener("DOMContentLoaded", function () {
        Livewire.on('refreshDeleteButton', (data) => {
            document.getElementById('deleteButton').disabled = !data.hasSelectedRows;
        });
    });

    function confirmDelete() {
        Swal.fire({
            title: "آیا مطمئن هستید؟",
            text: "این عملیات غیرقابل بازگشت است!",
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: "#d33",
            cancelButtonColor: "#3085d6",
            confirmButtonText: "بله، حذف شود!",
            cancelButtonText: "لغو",
        }).then((result) => {
            if (result.isConfirmed) {
                Livewire.dispatch('deleteConfirmed'); // ✅ اصلاح شد
            }
        });
    }

</script><?php /**PATH D:\MyProjects\Benobe\panel\resources\views/livewire/admin/dashboard/search-best-doctors.blade.php ENDPATH**/ ?>