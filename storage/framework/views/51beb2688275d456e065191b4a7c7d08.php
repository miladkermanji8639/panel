<?php $__env->startSection('vendor-style'); ?>
 <?php echo app('Illuminate\Foundation\Vite')(['resources/assets/vendor/libs/apex-charts/apex-charts.scss']); ?>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('vendor-script'); ?>
 <?php echo app('Illuminate\Foundation\Vite')(['resources/assets/vendor/libs/apex-charts/apexcharts.js']); ?>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('page-script'); ?>
 <?php echo app('Illuminate\Foundation\Vite')(['resources/assets/js/dashboards-crm.js']); ?>
<?php $__env->stopSection(); ?>
<link href="<?php echo e(asset('admin-assets/css/old-benobe-styles/bootstrap.min.css')); ?>" rel="stylesheet">
<link href="<?php echo e(asset('admin-assets/css/old-benobe-styles/bootstrap-rtl.min.css')); ?>" rel="stylesheet">
<link href="<?php echo e(asset('admin-assets/css/old-benobe-styles/app_admin.css?v=dddmue')); ?>" rel="stylesheet">
<style>
 a {
  color: #333 !important;
 }
</style>
<?php $__env->startSection('content'); ?>
  <?php
$__split = function ($name, $params = []) {
    return [$name, $params];
};
[$__name, $__params] = $__split('admin.dashboard.system-setting.settings-component', []);

$__html = app('livewire')->mount($__name, $__params, 'lw-1663291231-0', $__slots ?? [], get_defined_vars());

echo $__html;

unset($__html);
unset($__name);
unset($__params);
unset($__split);
if (isset($__slots)) unset($__slots);
?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('admin.content.layouts/layoutMaster', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\MyProjects\Benobe\panel\resources\views/admin/content/dashboard/setting/index.blade.php ENDPATH**/ ?>