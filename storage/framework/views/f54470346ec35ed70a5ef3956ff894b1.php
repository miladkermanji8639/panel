<div>
 <div class="card">
  <div class="card-header">
   <h5 class="card-title">افزودن پزشک برتر</h5>
  </div>
  <div class="card-body">
   <form wire:submit.prevent="save">
    <div class="form-group">
     <label>انتخاب پزشک:</label>
     <select id="doctor_select" wire:model="doctor_id" class="form-control">
      <option value="">انتخاب پزشک</option>
      <!--[if BLOCK]><![endif]--><?php $__currentLoopData = $doctors; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $doctor): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
       <option value="<?php echo e($doctor->id); ?>">
        <?php echo e($doctor->first_name . ' ' . $doctor->last_name . ' (' . $doctor->national_code . ')'); ?></option>
      <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><!--[if ENDBLOCK]><![endif]-->
     </select>
     <!--[if BLOCK]><![endif]--><?php $__errorArgs = ['doctor_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
      <span class="text-danger"><?php echo e($message); ?></span>
     <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><!--[if ENDBLOCK]><![endif]-->
    </div>

    <div class="form-group mt-3">
     <label>انتخاب بیمارستان:</label>
     <select id="hospital_select" wire:model="hospital_id" class="form-control">
      <option value="">انتخاب بیمارستان (اختیاری)</option>
      <!--[if BLOCK]><![endif]--><?php $__currentLoopData = $hospitals; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $hospital): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
       <option value="<?php echo e($hospital->id); ?>"><?php echo e($hospital->name); ?></option>
      <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><!--[if ENDBLOCK]><![endif]-->
     </select>
    </div>

    <div class="form-check mt-3">
     <input type="checkbox" wire:model="best_doctor" class="form-check-input">
     <label class="form-check-label">پزشک برتر</label>
    </div>

    <div class="form-check mt-2">
     <input type="checkbox" wire:model="best_consultant" class="form-check-input">
     <label class="form-check-label">مشاور تلفنی برتر</label>
    </div>

    <div class="mt-4">
     <button type="submit" class="btn btn-success">افزودن</button>
     <a href="<?php echo e(route('admin.Dashboard.home_page.index')); ?>" class="btn btn-secondary">بازگشت</a>
    </div>
   </form>
  </div>
 </div>

 <script>
  document.addEventListener("DOMContentLoaded", function() {
   new TomSelect("#doctor_select", {
    create: false,
    sortField: "text"
   });
   new TomSelect("#hospital_select", {
    create: false,
    sortField: "text"
   });
  });

  document.addEventListener("livewire:load", function() {
   new TomSelect("#doctor_select", {
    create: false,
    sortField: "text"
   });
   new TomSelect("#hospital_select", {
    create: false,
    sortField: "text"
   });
  });
 </script>
</div>
<?php /**PATH D:\MyProjects\Benobe\panel\resources\views/livewire/admin/dashboard/create-best-doctor.blade.php ENDPATH**/ ?>