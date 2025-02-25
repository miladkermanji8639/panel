

<?php $__env->startSection('styles'); ?>
 <link type="text/css" href="<?php echo e(asset('dr-assets/panel/css/panel.css')); ?>" rel="stylesheet" />
<?php $__env->stopSection(); ?>

<?php $__env->startSection('site-header'); ?>
 <?php echo e('به نوبه | ایجاد خدمت جدید'); ?>

<?php $__env->stopSection(); ?>
 <?php $__env->startSection('bread-crumb-title', ' افزودن خدمات'); ?>

<?php $__env->startSection('content'); ?>
   <div class="container my-4">
    <div class="card shadow-sm">
    <div class="card-header w-100 d-flex justify-content-between">
    <div>
      <h4>ایجاد خدمت</h4>

    </div>
    <div>
      <a href="<?php echo e(route('dr-services.index')); ?>" class="btn btn-info text-white">بازگشت</a>
    </div>
    </div>
     <div class="card-body">
    <form action="<?php echo e(route('dr-services.store')); ?>" method="POST">
    <?php echo csrf_field(); ?>
    <div class=" position-relative">
     <input type="hidden" name="doctor_id" id="doctor_id" class="form-control h-50" value="<?php echo e(Auth::guard('doctor')->user()->id); ?>">
    </div>
    <div class=" position-relative mb-5">
     <label class="label-top-input-special-takhasos" class="label-top-input-special-takhasos" for="name">نام خدمت</label>
     <input type="text" name="name" id="name" class="form-control h-50" placeholder="نام خدمت" required>
    </div>
    <div class=" position-relative mb-5">
     <label class="label-top-input-special-takhasos" for="description">توضیحات</label>
     <textarea name="description" id="description" class="form-control h-50" rows="3" placeholder="توضیحات خدمت"></textarea>
    </div>
    <div class=" position-relative mb-5">
     <label class="label-top-input-special-takhasos" for="duration">مدت زمان خدمت (دقیقه)</label>
     <input type="number" name="duration" id="duration" class="form-control h-50" placeholder="مثلاً 60" required>
    </div>
    <div class=" position-relative mb-5">
     <label class="label-top-input-special-takhasos" for="price">قیمت</label>
     <input type="number" step="0.01" name="price" min="0" max="90000000000" id="price" class="form-control h-50" placeholder="قیمت خدمت" required>
    </div>
    <div class=" position-relative mb-5">
     <label class="label-top-input-special-takhasos" for="discount">تخفیف اختیاری</label>
     <input type="number" step="0.01" name="discount" id="discount" class="form-control h-50" placeholder="در صورت وجود">
    </div>
    <div class=" position-relative mb-5">
     <label class="label-top-input-special-takhasos" for="status">وضعیت</label>
     <select name="status" id="status" class="form-control h-50" required>
    <option value="active">فعال</option>
    <option value="inactive">غیرفعال</option>
    <option value="pending">در انتظار</option>
     </select>
    </div>
    <div class=" position-relative mb-2">
     <label class="label-top-input-special-takhasos" for="parent_id">زیرگروه  (در صورت وجود)</label>
     <select name="parent_id" id="parent_id" class="form-control h-50">
    <option value="">-- انتخاب خدمت  --</option>
    <?php $__currentLoopData = $parentServices; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $service): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
    <option value="<?php echo e($service->id); ?>"><?php echo e($service->name); ?></option>
   <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
     </select>
    </div>
    <button type="submit" class="btn btn-primary w-100 mt-2 h-50">ثبت خدمت</button>
    </form>
     </div>
    </div>
   </div>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('scripts'); ?>
   <script src="<?php echo e(asset('dr-assets/panel/js/dr-panel.js')); ?>"></script>
   <script>
    $(document).ready(function () {
      let dropdownOpen = false;
      let selectedClinic = localStorage.getItem('selectedClinic');
      let selectedClinicId = localStorage.getItem('selectedClinicId');
      if (selectedClinic && selectedClinicId) {
        $('.dropdown-label').text(selectedClinic);
        $('.option-card').each(function () {
        if ($(this).attr('data-id') === selectedClinicId) {
          $('.option-card').removeClass('card-active');
          $(this).addClass('card-active');
        }
        });
      } else {
        localStorage.setItem('selectedClinic', 'ویزیت آنلاین به نوبه');
        localStorage.setItem('selectedClinicId', 'default');
      }

      function checkInactiveClinics() {
        var hasInactiveClinics = $('.option-card[data-active="0"]').length > 0;
        if (hasInactiveClinics) {
        $('.dropdown-trigger').addClass('warning');
        } else {
        $('.dropdown-trigger').removeClass('warning');
        }
      }
      checkInactiveClinics();

      $('.dropdown-trigger').on('click', function (event) {
        event.stopPropagation();
        dropdownOpen = !dropdownOpen;
        $(this).toggleClass('border border-primary');
        $('.my-dropdown-menu').toggleClass('d-none');
        setTimeout(() => {
        dropdownOpen = $('.my-dropdown-menu').is(':visible');
        }, 100);
      });

      $(document).on('click', function () {
        if (dropdownOpen) {
        $('.dropdown-trigger').removeClass('border border-primary');
        $('.my-dropdown-menu').addClass('d-none');
        dropdownOpen = false;
        }
      });

      $('.my-dropdown-menu').on('click', function (event) {
        event.stopPropagation();
      });

      $('.option-card').on('click', function () {
        var selectedText = $(this).find('.font-weight-bold.d-block.fs-15').text().trim();
        var selectedId = $(this).attr('data-id');
        $('.option-card').removeClass('card-active');
        $(this).addClass('card-active');
        $('.dropdown-label').text(selectedText);

        localStorage.setItem('selectedClinic', selectedText);
        localStorage.setItem('selectedClinicId', selectedId);
        checkInactiveClinics();
        $('.dropdown-trigger').removeClass('border border-primary');
        $('.my-dropdown-menu').addClass('d-none');
        dropdownOpen = false;

        // ریلود صفحه با پارامتر جدید
        window.location.href = window.location.pathname + "?selectedClinicId=" + selectedId;
      });
      });
   </script>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('dr.panel.layouts.master', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\MyProjects\Benobe\panel\resources\views/dr/panel/dr-services/create.blade.php ENDPATH**/ ?>