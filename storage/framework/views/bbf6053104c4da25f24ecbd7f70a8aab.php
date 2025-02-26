
<?php $__env->startSection('styles'); ?>
 <link rel="stylesheet" href="<?php echo e(asset('dr-assets/panel/css/panel.css')); ?>">
 <link rel="stylesheet" href="<?php echo e(asset('dr-assets/panel/css/turn/schedule/scheduleSetting/scheduleSetting.css')); ?>">
 <link rel="stylesheet" href="<?php echo e(asset('dr-assets/panel/profile/edit-profile.css')); ?>">
 <link rel="stylesheet" href="<?php echo e(asset('dr-assets/panel/css/secretary_options/secretary_option.css')); ?>">
<?php $__env->stopSection(); ?>
<?php $__env->startSection('site-header', 'به نوبه | پنل دکتر'); ?>
<?php $__env->startSection('content'); ?>
<?php $__env->startSection('bread-crumb-title', 'مدیریت دسترسی‌ها'); ?>
<div class="container">
 <div class="">
  <div class="">
   <form id="permissions-form">
    <div class="table-responsive">
     <table class="table table-bordered">
      <thead class="table-dark">
       <tr>
        <th>نام منشی</th>
        <th>دسترسی‌ها</th>
       </tr>
      </thead>
      <tbody>
       <?php $__currentLoopData = $secretaries; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $secretary): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <?php
 $savedPermissions = json_decode($secretary->permissions->permissions ?? '[]', true);
 $savedPermissions = is_array($savedPermissions) ? $savedPermissions : []; // اطمینان از آرایه بودن
        ?>
        <tr>
         <td><?php echo e($secretary->first_name); ?> <?php echo e($secretary->last_name); ?></td>
         <td>
          <div class="form-check w-100 my-check-wrapper" style="text-align: right">
           <?php $__currentLoopData = $permissions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $permissionKey => $permissionData): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <div class="mb-2 d-flex align-items-center">
             <input type="checkbox" class="form-check-input parent-permission update-permissions substituted"
              data-secretary-id="<?php echo e($secretary->id); ?>" value="<?php echo e($permissionKey); ?>"
              <?php echo e(in_array($permissionKey, $savedPermissions) ? 'checked' : ''); ?>>
             <label class="form-check-label font-weight-bold mx-1"><?php echo e($permissionData['title']); ?></label>
            </div>
            <?php if(!empty($permissionData['routes'])): ?>
             <div class="ml-3">
              <?php $__currentLoopData = $permissionData['routes']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $routeKey => $routeTitle): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
               <div class="d-flex align-items-center">
                <input type="checkbox" class="form-check-input child-permission update-permissions substituted"
                 data-secretary-id="<?php echo e($secretary->id); ?>" data-parent="<?php echo e($permissionKey); ?>"
                 value="<?php echo e($routeKey); ?>" <?php echo e(in_array($routeKey, $savedPermissions) ? 'checked' : ''); ?>>
                <label class="form-check-label mx-1"><?php echo e($routeTitle); ?></label>
               </div>
              <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
             </div>
            <?php endif; ?>
           <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
          </div>
         </td>
        </tr>
       <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
      </tbody>
     </table>
    </div>
   </form>
  </div>
 </div>
</div>
<?php $__env->stopSection(); ?>
<?php $__env->startSection('scripts'); ?>
 <script src="<?php echo e(asset('dr-assets/panel/jalali-datepicker/run-jalali.js')); ?>"></script>
 <script src="<?php echo e(asset('dr-assets/panel/js/dr-panel.js')); ?>"></script>
 <script src="<?php echo e(asset('dr-assets/panel/js/turn/scehedule/sheduleSetting/workhours/workhours.js')); ?>"></script>
 <script>
  $(document).ready(function() {
   let dropdownOpen = false;
   let selectedClinic = localStorage.getItem('selectedClinic');
   let selectedClinicId = localStorage.getItem('selectedClinicId');
   if (selectedClinic && selectedClinicId) {
    $('.dropdown-label').text(selectedClinic);
    $('.option-card').each(function() {
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

   $('.dropdown-trigger').on('click', function(event) {
    event.stopPropagation();
    dropdownOpen = !dropdownOpen;
    $(this).toggleClass('border border-primary');
    $('.my-dropdown-menu').toggleClass('d-none');
    setTimeout(() => {
  dropdownOpen = $('.my-dropdown-menu').is(':visible');
    }, 100);
   });

   $(document).on('click', function() {
    if (dropdownOpen) {
  $('.dropdown-trigger').removeClass('border border-primary');
  $('.my-dropdown-menu').addClass('d-none');
  dropdownOpen = false;
    }
   });

   $('.my-dropdown-menu').on('click', function(event) {
    event.stopPropagation();
   });

   $('.option-card').on('click', function() {
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
  $(document).ready(function() {
   let updateTimer; // متغیر برای مدیریت debounce
   // تابع برای ارسال درخواست AJAX
   function updatePermissions(secretaryId) {
    let permissions = [];
    let selectedClinicId = localStorage.getItem('selectedClinicId') || 'default';

    $('input[data-secretary-id="' + secretaryId + '"]:checked').each(function () {
  permissions.push($(this).val());
    });

    $.ajax({
  url: "<?php echo e(route('dr-secretary-permissions-update', ':id')); ?>".replace(':id', secretaryId),
  method: "POST",
  data: {
   permissions: permissions,
   selectedClinicId: selectedClinicId,
   _token: "<?php echo e(csrf_token()); ?>"
  },
  success: function (response) {
   if (response.success) {
    toastr.success(response.message);
   }
  },
  error: function () {
   toastr.error('مشکلی در ذخیره اطلاعات پیش آمد.');
  }
    });
   }

   // مدیریت ارتباط بین والد و فرزند
   $('.parent-permission').change(function() {
    let isChecked = $(this).is(':checked');
    let parentKey = $(this).val();
    let secretaryId = $(this).data('secretary-id');
    $(this).closest('td').find(`.child-permission[data-parent="${parentKey}"]`).prop('checked', isChecked);
    clearTimeout(updateTimer);
    updateTimer = setTimeout(() => updatePermissions(secretaryId), 500);
   });
   // بروزرسانی سطوح دسترسی با debounce
   $('.update-permissions').change(function() {
    let secretaryId = $(this).data('secretary-id');
    clearTimeout(updateTimer);
    updateTimer = setTimeout(() => updatePermissions(secretaryId), 500);
   });
  });
 </script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('dr.panel.layouts.master', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\MyProjects\Benobe\panel\resources\views/dr/panel/secretary_permissions/index.blade.php ENDPATH**/ ?>