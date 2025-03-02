<?php $__env->startSection('title', 'انقضای صفحه - به نوبه'); ?>

<?php $__env->startSection('content'); ?>
 <span class="error-icon">⏳</span>
 <p class="text-gray-500 mt-4">صفحه شما منقضی شده. لطفاً دوباره تلاش کنید، مثل یه نوبت که باید تازه بشه!</p>
<?php $__env->stopSection(); ?>

<?php
$code = '419';
$message = 'انقضای توکن';
?>
<?php echo $__env->make('errors.layout', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\MyProjects\Benobe\panel\resources\views/errors/419.blade.php ENDPATH**/ ?>