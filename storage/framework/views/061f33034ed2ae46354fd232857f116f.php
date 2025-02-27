<?php if(session('swal-success')): ?>

    <script>
        // $(document).ready(function (){
            Swal.fire({
                title: 'عملیات با موفقیت انجام شد',
                 text: '<?php echo e(session('swal-success')); ?>',
                 icon: 'success',
                 confirmButtonText: 'باشه',
      });
        // });
    </script>

<?php endif; ?>
<?php /**PATH D:\MyProjects\Benobe\panel\resources\views/admin/content/alerts/sweetalert/success.blade.php ENDPATH**/ ?>