<?php $__env->startSection('title', 'نظرات پزشکان - مدیریت نظرات'); ?>



<?php $__env->startSection('page-script'); ?>
  <?php echo app('Illuminate\Foundation\Vite')(['resources/assets/js/dashboards-crm.js']); ?>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
  <div class="container-fluid py-4">
    <header class="glass-header p-3 rounded-3 mb-2 shadow-lg">
    <div class="d-flex align-items-center justify-content-between gap-3">
      <div class="d-flex align-items-center gap-2">
      <i class="fas fa-comments fs-4 text-white animate-bounce"></i>
      <h4 class="mb-0 fw-bold text-white">نظرات پزشکان</h4>
      </div>
      <div class="text-white fw-medium fs-6">جستجو و مدیریت نظرات</div>
    </div>
    </header>

    <?php
$__split = function ($name, $params = []) {
    return [$name, $params];
};
[$__name, $__params] = $__split('admin.doctors.comment-doctor.admin-doctors-comment-index');

$__html = app('livewire')->mount($__name, $__params, 'lw-4119097107-0', $__slots ?? [], get_defined_vars());

echo $__html;

unset($__html);
unset($__name);
unset($__params);
unset($__split);
if (isset($__slots)) unset($__slots);
?>

  </div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('admin.content.layouts/layoutMaster', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\MyProjects\Benobe\panel\resources\views/admin/content/doctors/comment-doctor/index.blade.php ENDPATH**/ ?>