<?php $__env->startSection('title', 'صفحه پیدا نشد - به نوبه'); ?>

<?php $__env->startSection('content'); ?>
 <span class="error-icon">⏰</span>
 <p class="text-gray-500 mt-4">متأسفیم! انگار این صفحه مثل یه نوبت گم‌شده پیدا نمی‌شه.</p>
<?php $__env->stopSection(); ?>

<?php
$code = '404';
$message = 'صفحه مورد نظر یافت نشد';
?>
<?php echo $__env->make('errors.layout', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\MyProjects\Benobe\panel\resources\views/errors/404.blade.php ENDPATH**/ ?>