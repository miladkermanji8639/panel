<?php $__env->startSection('title', 'تعرفه حق عضویت پزشکان'); ?>

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





  <div class="card">
  <div class="card-header">
    <h5 class="card-title mb-0">لیست تعرفه‌ها</h5>
    
  </div>
  
  <div class="card-datatable table-responsive">
    <?php
$__split = function ($name, $params = []) {
    return [$name, $params];
};
[$__name, $__params] = $__split('admin.dashboard.membership.membership-fee-component');

$__html = app('livewire')->mount($__name, $__params, 'lw-2860869861-0', $__slots ?? [], get_defined_vars());

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
  <!-- / Content -->

  <!-- Footer -->
  <!-- Footer-->

  <!--/ Footer-->
  <!-- / Footer -->
  <div class="content-backdrop fade"></div>
   </div>


<?php $__env->stopSection(); ?>

<?php echo $__env->make('admin.content.layouts/layoutMaster', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\MyProjects\Benobe\panel\resources\views/admin/content/dashboard/membershipfee/index.blade.php ENDPATH**/ ?>