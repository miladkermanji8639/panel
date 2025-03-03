<?php $__env->startSection('title', 'دسترسی غیرمجاز - به نوبه'); ?>

<?php $__env->startSection('content'); ?>
 <span class="error-icon">🔒</span>
 <p class="text-gray-500 mt-4">شما اجازه ورود به این بخش رو ندارید. مثل یه کلینیک که فقط با نوبت کار می‌کنه!</p>
<?php $__env->stopSection(); ?>

<?php
$code = '403';
$message = 'دسترسی غیرمجاز';
?>
<?php echo $__env->make('errors.layout', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\MyProjects\Benobe\panel\resources\views/errors/403.blade.php ENDPATH**/ ?>