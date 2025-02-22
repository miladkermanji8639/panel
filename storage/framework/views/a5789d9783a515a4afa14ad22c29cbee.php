<?php if(session('swal-error')): ?>

    <script>
        // $(document).ready(function (){
            Swal.fire({
                title: 'خطا!',
                 text: '<?php echo e(session('swal-error')); ?>',
                 icon: 'error',
                 confirmButtonText: 'باشه',
      });
        // });
    </script>

<?php endif; ?>
<?php /**PATH D:\MyProjects\Benobe\panel\resources\views/admin/content/alerts/sweetalert/error.blade.php ENDPATH**/ ?>