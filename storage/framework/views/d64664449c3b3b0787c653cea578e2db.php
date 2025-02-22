<?php $__env->startSection('title', 'بسته ها '); ?>

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
 <div class="content-wrapper">

  <!-- Content -->
  <div class="flex-grow-1  container-fluid">


   <h4 class="py-3 mb-4">
    <span class="text-muted fw-light">بسته های حق عضویت /</span>
    لیست بسته های حق عضویت
   </h4>


   <div class="card">
    <div class="card-header">
     <h5 class="card-title mb-0">لیست بسته های حق عضویت</h5>

    </div>
    <div class="card-datatable table-responsive">
     <?php
$__split = function ($name, $params = []) {
    return [$name, $params];
};
[$__name, $__params] = $__split('usership-fee-component');

$__html = app('livewire')->mount($__name, $__params, 'lw-77516261-0', $__slots ?? [], get_defined_vars());

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
  <div class="content-backdrop fade"></div>
 </div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('admin.content.layouts/layoutMaster', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\MyProjects\Benobe\panel\resources\views/admin/content/dashboard/usershipfee/index.blade.php ENDPATH**/ ?>