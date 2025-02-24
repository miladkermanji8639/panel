

<?php $__env->startSection('styles'); ?>
 <link type="text/css" href="<?php echo e(asset('dr-assets/panel/css/panel.css')); ?>" rel="stylesheet" />
<?php $__env->stopSection(); ?>

<?php $__env->startSection('site-header'); ?>
 <?php echo e('به نوبه | خدمات دکتر'); ?>

<?php $__env->stopSection(); ?>
<?php $__env->startSection('bread-crumb-title', 'خدمات دکتر'); ?>

<?php $__env->startSection('content'); ?>
 <div class="container my-4">
  <div class="card shadow-sm">
   <div class="card-header d-flex justify-content-between align-items-center">
    <h4 class="mb-0">لیست خدمات دکتر</h4>
    <a href="<?php echo e(route('dr-services.create')); ?>" class="btn btn-primary">ایجاد خدمت جدید</a>
   </div>
   <div class="card-body">
    <?php
$__split = function ($name, $params = []) {
    return [$name, $params];
};
[$__name, $__params] = $__split('dr.panel.doctor-services.doctor-services');

$__html = app('livewire')->mount($__name, $__params, 'lw-3449959986-0', $__slots ?? [], get_defined_vars());

echo $__html;

unset($__html);
unset($__name);
unset($__params);
unset($__split);
if (isset($__slots)) unset($__slots);
?>
   </div>
  </div>
 </div>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('scripts'); ?>
 <script src="<?php echo e(asset('dr-assets/panel/js/dr-panel.js')); ?>"></script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('dr.panel.layouts.master', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\MyProjects\Benobe\panel\resources\views/dr/panel/dr-services/index.blade.php ENDPATH**/ ?>