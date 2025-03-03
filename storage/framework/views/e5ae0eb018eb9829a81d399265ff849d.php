<?php $__env->startSection('title', 'مدیریت پزشکان'); ?>

<?php $__env->startSection('vendor-style'); ?>
    <?php echo app('Illuminate\Foundation\Vite')(['resources/assets/vendor/libs/apex-charts/apex-charts.scss']); ?>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('vendor-script'); ?>
    <?php echo app('Illuminate\Foundation\Vite')(['resources/assets/vendor/libs/apex-charts/apexcharts.js']); ?>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('page-script'); ?>
    <?php echo app('Illuminate\Foundation\Vite')(['resources/assets/js/dashboards-crm.js']); ?>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
    <div class="container-fluid py-1">
        <header class="glass-header p-3 rounded-3 mb-2 shadow-lg">
            <div class="d-flex align-items-center justify-content-between gap-3">
                <div class="d-flex align-items-center gap-2">
                    <i class="fas fa-user-md fs-4 text-white animate-bounce"></i>
                    <h4 class="mb-0 fw-bold text-white">مدیریت پزشکان</h4>
                </div>
                <div>
                    <a href="<?php echo e(route('admin.doctors.doctors-management.create')); ?>" class="btn btn-light">ایجاد پزشک</a>
                </div>
            </div>
        </header>

        <?php
$__split = function ($name, $params = []) {
    return [$name, $params];
};
[$__name, $__params] = $__split('admin.doctors.doctors-management-list');

$__html = app('livewire')->mount($__name, $__params, 'lw-1922655604-0', $__slots ?? [], get_defined_vars());

echo $__html;

unset($__html);
unset($__name);
unset($__params);
unset($__split);
if (isset($__slots)) unset($__slots);
?>
    </div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('admin.content.layouts.layoutMaster', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\MyProjects\Benobe\panel\resources\views/admin/content/doctors/doctors-management/index.blade.php ENDPATH**/ ?>