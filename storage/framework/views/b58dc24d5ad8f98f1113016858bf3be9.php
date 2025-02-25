<?php $__env->startSection('styles'); ?>
    <link type="text/css" href="<?php echo e(asset('dr-assets/panel/css/panel.css')); ?>" rel="stylesheet" />
    <link type="text/css" href="<?php echo e(asset('dr-assets/panel/profile/edit-profile.css')); ?>" rel="stylesheet" />
    <link type="text/css" href="<?php echo e(asset('dr-assets/panel/css/payment/setting.css')); ?>" rel="stylesheet" />
<?php $__env->stopSection(); ?>
<?php $__env->startSection('site-header'); ?>
    <?php echo e('به نوبه | پنل دکتر'); ?>

<?php $__env->stopSection(); ?>
<?php $__env->startSection('content'); ?>
<?php $__env->startSection('bread-crumb-title', 'پرداخت'); ?>
    <?php
$__split = function ($name, $params = []) {
    return [$name, $params];
};
[$__name, $__params] = $__split('dr.payment-setting-component', []);

$__html = app('livewire')->mount($__name, $__params, 'lw-133917438-0', $__slots ?? [], get_defined_vars());

echo $__html;

unset($__html);
unset($__name);
unset($__params);
unset($__split);
if (isset($__slots)) unset($__slots);
?>
<?php $__env->stopSection(); ?>
<?php $__env->startSection('scripts'); ?>
    <script src="<?php echo e(asset('dr-assets/panel/jalali-datepicker/run-jalali.js')); ?>"></script>
    <script src="<?php echo e(asset('dr-assets/panel/js/dr-panel.js')); ?>"></script>
    <script src="<?php echo e(asset('dr-assets/panel/js/payment/setting.js')); ?>"></script>
    <script>
        var appointmentsSearchUrl = "<?php echo e(route('search.appointments')); ?>";
        var updateStatusAppointmentUrl = "<?php echo e(route('updateStatusAppointment', ':id')); ?>";
    </script>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('dr.panel.layouts.master', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\MyProjects\Benobe\panel\resources\views/dr/panel/payment/setting.blade.php ENDPATH**/ ?>