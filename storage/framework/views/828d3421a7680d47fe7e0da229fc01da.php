<!DOCTYPE html>
<html lang="fa-IR" dir="rtl" class="scroll-smooth">

<head>
 <?php echo $__env->make('dr.layouts.partials.head-tag', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
 <?php echo $__env->yieldContent('head-tag'); ?>
 <?php echo $__env->yieldContent('site-header'); ?>
</head>

<body>

 <main class="">

  <?php echo $__env->yieldContent('content'); ?>

 </main>

 <?php echo $__env->make('dr.layouts.partials.scripts', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
 <?php echo $__env->yieldContent('scripts'); ?>

</body>

</html>
<?php /**PATH D:\MyProjects\Benobe\panel\resources\views/dr/layouts/master-login.blade.php ENDPATH**/ ?>