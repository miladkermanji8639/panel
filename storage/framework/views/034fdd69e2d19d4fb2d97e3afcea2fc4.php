<!DOCTYPE html>
<?php
$menuFixed =
      $configData['layout'] === 'vertical'
      ? $menuFixed ?? ''
      : ($configData['layout'] === 'front'
            ? ''
            : $configData['headerType']);
$navbarType =
      $configData['layout'] === 'vertical'
      ? $configData['navbarType'] ?? ''
      : ($configData['layout'] === 'front'
            ? 'layout-navbar-fixed'
            : '');
$isFront = ($isFront ?? '') == true ? 'Front' : '';
$contentLayout = isset($container) ? ($container === 'container-xxl' ? 'layout-compact' : 'layout-wide') : '';
?>

<html lang="<?php echo e(session()->get('locale') ?? app()->getLocale()); ?>"
 class="<?php echo e($configData['style']); ?>-style <?php echo e($contentLayout ?? ''); ?> <?php echo e($navbarType ?? ''); ?> <?php echo e($menuFixed ?? ''); ?> <?php echo e($menuCollapsed ?? ''); ?> <?php echo e($menuFlipped ?? ''); ?> <?php echo e($menuOffcanvas ?? ''); ?> <?php echo e($footerFixed ?? ''); ?> <?php echo e($customizerHidden ?? ''); ?>"
 dir="<?php echo e($configData['textDirection']); ?>" data-theme="<?php echo e($configData['theme']); ?>"
 data-assets-path="<?php echo e(asset('/assets') . '/'); ?>" data-base-url="<?php echo e(url('/')); ?>" data-framework="laravel"
 data-template="<?php echo e($configData['layout'] . '-menu-' . $configData['theme'] . '-' . $configData['styleOpt']); ?>">

<head>
 <meta charset="utf-8" />
 <meta name="viewport"
  content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0" />

 <title><?php echo $__env->yieldContent('title'); ?> |
  <?php echo e(config('variables.templateName') ? config('variables.templateName') : 'TemplateName'); ?> -
  <?php echo e(config('variables.templateSuffix') ? config('variables.templateSuffix') : 'TemplateSuffix'); ?>

 </title>
 <meta name="description"
  content="<?php echo e(config('variables.templateDescription') ? config('variables.templateDescription') : ''); ?>" />
 <meta name="keywords" content="<?php echo e(config('variables.templateKeyword') ? config('variables.templateKeyword') : ''); ?>">
 <!-- laravel CRUD token -->
 <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">
 <!-- Canonical SEO -->
 <link rel="canonical" href="<?php echo e(config('variables.productPage') ? config('variables.productPage') : ''); ?>">
 <!-- Favicon -->
 <link rel="icon" type="image/x-icon" href="<?php echo e(asset('assets/img/favicon/favicon.ico')); ?>" />



 <!-- Include Styles -->
 <!-- $isFront is used to append the front layout styles only on the front layout otherwise the variable will be blank -->
 <?php echo $__env->make('admin.content.layouts/sections/styles' . $isFront, array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

 <!-- Include Scripts for customizer, helper, analytics, config -->
 <!-- $isFront is used to append the front layout scriptsIncludes only on the front layout otherwise the variable will be blank -->
 <?php echo $__env->make('admin.content.layouts/sections/scriptsIncludes' . $isFront, array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>



 <?php echo \Livewire\Mechanisms\FrontendAssets\FrontendAssets::styles(); ?>

</head>

<body>

 <!-- Layout Content -->
 <?php echo $__env->yieldContent('layoutContent'); ?>
 <!--/ Layout Content -->



 <!-- Include Scripts -->
 <!-- $isFront is used to append the front layout scripts only on the front layout otherwise the variable will be blank -->
 <?php echo $__env->make('admin.content.layouts/sections/scripts' . $isFront, array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>




 
 <?php echo $__env->make('admin.content.alerts.sweetalert.success', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
 <?php echo $__env->make('admin.content.alerts.sweetalert.error', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
 <?php echo $__env->make('admin.content.alerts.sweetalert.delete-confirm', ['className' => 'delete'], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
 
 <?php echo \Livewire\Mechanisms\FrontendAssets\FrontendAssets::scripts(); ?>

 <script>
  document.addEventListener("DOMContentLoaded", function() {
   Livewire.on('refreshDeleteButton', (data) => {
    document.getElementById('deleteButton').disabled = !data.hasSelectedRows;
   });
  });
 </script>

 <script>
  document.addEventListener('DOMContentLoaded', function() {
   Livewire.on('show-toastr', (data) => {
    toastr.options = {
     progressBar: true,
     positionClass: "toast-top-right", // نمایش در سمت راست بالا
     timeOut: 3000 // زمان نمایش
    };

    if (data.type === 'success') {
     toastr.success(data.message);
    } else {
     toastr.warning(data.message);
    }
   });
  });
 </script>
<script>
    document.addEventListener("DOMContentLoaded", function () {
        Livewire.on('show-delete-confirmation', () => {
            Swal.fire({
                title: "آیا مطمئن هستید؟",
                text: "این عملیات غیرقابل بازگشت است!",
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#d33",
                cancelButtonColor: "#3085d6",
                confirmButtonText: "بله، حذف شود!",
                cancelButtonText: "لغو"
            }).then((result) => {
                if (result.isConfirmed) {
                    Livewire.dispatch('doDeleteSelected');
                }
            });
        });
    });
</script>


</body>

</html>
<?php /**PATH D:\MyProjects\Benobe\panel\resources\views/admin/content/layouts/commonMaster.blade.php ENDPATH**/ ?>