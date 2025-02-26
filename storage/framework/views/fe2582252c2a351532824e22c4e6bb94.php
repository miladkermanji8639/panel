

<?php $__env->startSection('styles'); ?>
  <link type="text/css" href="<?php echo e(asset('dr-assets/panel/css/panel.css')); ?>" rel="stylesheet" />
  <style>
    .myPanelOption{
      display: none
    }
  </style>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('site-header'); ?>
  <?php echo e('به نوبه | ویرایش خدمت '); ?>

<?php $__env->stopSection(); ?>

<?php $__env->startSection('bread-crumb-title', ' ویرایش خدمات'); ?>

<?php $__env->startSection('content'); ?>
  <div class="container my-4">
  <div class="card shadow-sm">
  <div class="card-header w-100 d-flex justify-content-between">
    <div>
    <h4>ویرایش خدمت</h4>

    </div>
    <div>
      <a href="<?php echo e(route('dr-services.index')); ?>" class="btn btn-info text-white">بازگشت</a>
    </div>
  </div>
  <div class="card-body">
    <form action="<?php echo e(route('dr-services.update', $service->id)); ?>" method="POST">
    <?php echo csrf_field(); ?>
    <?php echo method_field('PUT'); ?>
    <div class="position-relative">
    <input type="hidden" name="doctor_id" id="doctor_id" class="form-control h-50"
    value="<?php echo e(Auth::guard('doctor')->user()->id); ?>">
    </div>
    <div class="position-relative mb-5">
    <label class="label-top-input-special-takhasos" for="name">نام خدمت</label>
    <input type="text" name="name" id="name" class="form-control h-50" placeholder="نام خدمت"
    value="<?php echo e(old('name', $service->name)); ?>" required>
    </div>
    <div class="position-relative mb-5">
    <label class="label-top-input-special-takhasos" for="description">توضیحات</label>
    <textarea name="description" id="description" class="form-control h-50" rows="3"
    placeholder="توضیحات خدمت"><?php echo e(old('description', $service->description)); ?></textarea>
    </div>
    <div class="position-relative mb-5">
    <label class="label-top-input-special-takhasos" for="duration">مدت زمان خدمت (دقیقه)</label>
    <input type="number" name="duration" id="duration" class="form-control h-50" placeholder="مثلاً 60"
    value="<?php echo e(old('duration', $service->duration)); ?>" required>
    </div>
    <div class="position-relative mb-5">
    <label class="label-top-input-special-takhasos" for="price">قیمت</label>
    <input type="number" min="0" max="90000000000" step="0.01" name="price" id="price" class="form-control h-50" placeholder="قیمت خدمت"
    value="<?php echo e(old('price', $service->price)); ?>" required>
    </div>
    <div class="position-relative mb-5">
    <label class="label-top-input-special-takhasos" for="discount">تخفیف اختیاری</label>
    <input type="number" step="0.01" name="discount" id="discount" class="form-control h-50"
    placeholder="تومان (در صورت وجود)" value="<?php echo e(old('discount', $service->discount)); ?>">
    </div>
    <div class="position-relative mb-5">
    <label class="label-top-input-special-takhasos" for="status">وضعیت</label>
    <select name="status" id="status" class="form-control h-50" required>
    <option value="active" <?php echo e(old('status', $service->status) == 1 ? 'selected' : ''); ?>>فعال</option>
    <option value="inactive" <?php echo e(old('status', $service->status) == 0 ? 'selected' : ''); ?>>غیرفعال
    </option>
    </select>
    </div>
    <div class="position-relative mb-2">
    <label class="label-top-input-special-takhasos" for="parent_id">زیرگروه (در صورت وجود)</label>
    <select name="parent_id" id="parent_id" class="form-control h-50">
    <option value="">-- انتخاب خدمت --</option>
    <?php $__currentLoopData = $parentServices; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $parentService): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
    <option value="<?php echo e($parentService->id); ?>" <?php echo e(old('parent_id', $service->parent_id) == $parentService->id ? 'selected' : ''); ?>>
    <?php echo e($parentService->name); ?>

    </option>
  <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </select>
    </div>
    <button type="submit" class="btn btn-primary w-100 mt-2 h-50">ویرایش خدمت</button>
    </form>
  </div>
  </div>
  </div>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('scripts'); ?>
  <script src="<?php echo e(asset('dr-assets/panel/js/dr-panel.js')); ?>"></script>
  <script>
    
  </script>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('dr.panel.layouts.master', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\MyProjects\Benobe\panel\resources\views/dr/panel/dr-services/edit.blade.php ENDPATH**/ ?>