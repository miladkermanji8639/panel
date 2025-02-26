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
   <style>
    .panel-default {
     border-color: #e2e2e2;
    }

    .panel {
     border-radius: 10px;
     border: 1px solid;
     overflow: hidden;
     right: 0;
     margin-top: 5px;
     padding: 8px;
    }

    .panel-primary {
     border-color: #2d67a7;
     background-color: rgba(240, 246, 251, .47);
    }
   </style>
   <div class="app-content-body">

    <div class="bg-white-only lter b-b wrapper-md clrfix">

     <h1 class="m-n h3 font-thin">پزشک ها</h1>

    </div>
    <div class="wrapper-md">
   <?php
$__split = function ($name, $params = []) {
    return [$name, $params];
};
[$__name, $__params] = $__split('admin.dashboard.create-best-doctor');

$__html = app('livewire')->mount($__name, $__params, 'lw-515072028-0', $__slots ?? [], get_defined_vars());

echo $__html;

unset($__html);
unset($__name);
unset($__params);
unset($__split);
if (isset($__slots)) unset($__slots);
?>

    </div>
   </div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('admin.content.layouts/layoutMaster', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\MyProjects\Benobe\panel\resources\views/admin/content/dashboard/home_page/create.blade.php ENDPATH**/ ?>