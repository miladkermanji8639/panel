<script src="<?php echo e(asset('admin-assets/js/jquery/jquery.min.js')); ?>"></script>

<?php echo app('Illuminate\Foundation\Vite')(['resources/assets/vendor/fonts/tabler-icons.scss', 'resources/assets/vendor/fonts/fontawesome.scss', 'resources/assets/vendor/fonts/flag-icons.scss']); ?>
<!-- Core CSS -->
<?php echo app('Illuminate\Foundation\Vite')(['resources/assets/vendor/scss' . $configData['rtlSupport'] . '/core' . ($configData['style'] !== 'light' ? '-' . $configData['style'] : '') . '.scss', 'resources/assets/vendor/scss' . $configData['rtlSupport'] . '/' . $configData['theme'] . ($configData['style'] !== 'light' ? '-' . $configData['style'] : '') . '.scss', 'resources/assets/css/demo.css']); ?>
<?php echo app('Illuminate\Foundation\Vite')(['resources/assets/vendor/libs/node-waves/node-waves.scss', 'resources/assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.scss', 'resources/assets/vendor/libs/typeahead-js/typeahead.scss']); ?>
<!-- Vendor Styles -->
<?php echo $__env->yieldContent('vendor-style'); ?>
<!-- Page Styles -->
<?php echo $__env->yieldContent('page-style'); ?>

<link rel="stylesheet" href="<?php echo e(asset('admin-assets/css/choosen/choosen.min.css')); ?>">
<link rel="stylesheet" href="<?php echo e(asset('dr-assets/panel/css/toastr/toastr.min.css')); ?>">
<link rel="stylesheet" href="<?php echo e(asset('dr-asset/panel/css/tom-select.bootstrap5.min.css')); ?>">
<style>
      .h-50{
            height: 50px !important;
      }
</style><?php /**PATH D:\MyProjects\Benobe\panel\resources\views/admin/content/layouts/sections/styles.blade.php ENDPATH**/ ?>