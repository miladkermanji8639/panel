<?php $__env->startSection('vendor-style'); ?>
<?php echo app('Illuminate\Foundation\Vite')([
  'resources/assets/vendor/libs/fullcalendar/fullcalendar.scss',
  'resources/assets/vendor/libs/flatpickr/flatpickr.scss',
  'resources/assets/vendor/libs/select2/select2.scss',
  'resources/assets/vendor/libs/quill/editor.scss',
  'resources/assets/vendor/libs/@form-validation/form-validation.scss',
]); ?>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('page-style'); ?>
<?php echo app('Illuminate\Foundation\Vite')(['resources/assets/vendor/scss/pages/app-calendar.scss']); ?>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('vendor-script'); ?>
<?php echo app('Illuminate\Foundation\Vite')([
  'resources/assets/vendor/libs/fullcalendar/fullcalendar.js',
  'resources/assets/vendor/libs/@form-validation/popular.js',
  'resources/assets/vendor/libs/@form-validation/bootstrap5.js',
  'resources/assets/vendor/libs/@form-validation/auto-focus.js',
  'resources/assets/vendor/libs/select2/select2.js',
  'resources/assets/vendor/libs/jdate/jdate.min.js',
  'resources/assets/vendor/libs/flatpickr/flatpickr-jdate.js',
  'resources/assets/vendor/libs/flatpickr-jalali/dist/l10n/fa.js',
  'resources/assets/vendor/libs/moment/moment.js',
]); ?>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('page-script'); ?>
<?php echo app('Illuminate\Foundation\Vite')([
  'resources/assets/js/app-calendar-jalali.js',
]); ?>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
  <?php
$__split = function ($name, $params = []) {
    return [$name, $params];
};
[$__name, $__params] = $__split('admin.dashboard.holiday-manager');

$__html = app('livewire')->mount($__name, $__params, 'lw-750665510-0', $__slots ?? [], get_defined_vars());

echo $__html;

unset($__html);
unset($__name);
unset($__params);
unset($__split);
if (isset($__slots)) unset($__slots);
?>

<?php $__env->stopSection(); ?>
<?php echo $__env->make('admin.content.layouts/layoutMaster', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\MyProjects\Benobe\panel\resources\views/admin/content/dashboard/holiday/index.blade.php ENDPATH**/ ?>