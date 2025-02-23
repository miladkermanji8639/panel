<div>
 <h4 class="py-3 mb-4">
  <span class="text-muted fw-light">تخصص‌ها /</span> اضافه کردن تخصص جدید
 </h4>

 <!--[if BLOCK]><![endif]--><?php if($successMessage): ?>
  <div class="alert alert-success">
   <?php echo e($successMessage); ?>

  </div>
  <script>
   setTimeout(function () {
    window.location.href = "<?php echo e(route('admin.Dashboard.specialty.index')); ?>";
   }, 5000);
  </script>
 <?php endif; ?><!--[if ENDBLOCK]><![endif]-->

 <div class="card">
  <div class="card-header">
   <h5 class="card-title">اضافه کردن تخصص</h5>
  </div>
  <div class="card-body">
   <form wire:submit.prevent="store">
    <div class="row">
     <div class="col-md-12">
      <label class="form-label">نام تخصص <span class="text-danger">*</span></label>
      <input type="text" class="form-control" wire:model="name" placeholder="مثلا: متخصص قلب">
      <!--[if BLOCK]><![endif]--><?php $__errorArgs = ['name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <span class="text-danger"><?php echo e($message); ?></span> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><!--[if ENDBLOCK]><![endif]-->
     </div>

     <input type="hidden" wire:model.lazy="level" value="1">

     <div class="col-12 mt-4">
      <button type="submit" class="btn btn-success">اضافه کردن</button>
      <a href="<?php echo e(route('admin.Dashboard.specialty.index')); ?>" class="btn btn-secondary">بازگشت</a>
     </div>
    </div>
   </form>
  </div>
 </div>
</div><?php /**PATH D:\MyProjects\Benobe\panel\resources\views/livewire/admin/dashboard/specialties/specialty-create.blade.php ENDPATH**/ ?>