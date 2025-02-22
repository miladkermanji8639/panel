

<?php if(isset($pageConfigs)): ?>
<?php echo Helper::updatePageConfig($pageConfigs); ?>

<?php endif; ?>
<?php
$configData = Helper::appClasses();
?>

<?php if(isset($configData["layout"])): ?>
  <?php echo $__env->make((($configData["layout"] === 'horizontal') ? 'admin.content.layouts.horizontalLayout' :
    (($configData["layout"] === 'blank') ? 'admin.content.layouts.blankLayout' :
      (($configData["layout"] === 'front') ? 'admin.content.layouts.layoutFront' : 'admin.content.layouts.contentNavbarLayout'))), array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

<?php endif; ?>





<?php /**PATH D:\MyProjects\Benobe\panel\resources\views/admin/content/layouts/layoutMaster.blade.php ENDPATH**/ ?>