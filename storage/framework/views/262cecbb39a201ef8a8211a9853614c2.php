<?php $__env->startSection('styles'); ?>
 <link type="text/css" href="<?php echo e(asset('dr-assets/panel/css/panel.css')); ?>" rel="stylesheet" />
 <link type="text/css" href="<?php echo e(asset('dr-assets/panel/css/turn/schedule/scheduleSetting/scheduleSetting.css')); ?>"
  rel="stylesheet" />
 <link type="text/css" href="<?php echo e(asset('dr-assets/panel/profile/edit-profile.css')); ?>" rel="stylesheet" />
 <link type="text/css" href="<?php echo e(asset('dr-assets/panel/css/turn/schedule/scheduleSetting/workhours.css')); ?>"
  rel="stylesheet" />
 <link type="text/css" href="<?php echo e(asset('dr-assets/panel/bime/bime.css')); ?>" rel="stylesheet" />
<?php $__env->stopSection(); ?>
<?php $__env->startSection('site-header'); ?>
 <?php echo e('به نوبه | پنل دکتر'); ?>

<?php $__env->stopSection(); ?>
<?php $__env->startSection('content'); ?>
<?php $__env->startSection('bread-crumb-title', 'بیمه'); ?>

<div class="main-content">
 <?php
$__split = function ($name, $params = []) {
    return [$name, $params];
};
[$__name, $__params] = $__split('dr.panel.insurance.insurance-component');

$__html = app('livewire')->mount($__name, $__params, 'lw-3959226704-0', $__slots ?? [], get_defined_vars());

echo $__html;

unset($__html);
unset($__name);
unset($__params);
unset($__split);
if (isset($__slots)) unset($__slots);
?>
</div>
<?php $__env->stopSection(); ?>
<?php $__env->startSection('scripts'); ?>
<script src="<?php echo e(asset('dr-assets/panel/jalali-datepicker/run-jalali.js')); ?>"></script>
<script src="<?php echo e(asset('dr-assets/panel/js/dr-panel.js')); ?>"></script>
<script src="<?php echo e(asset('dr-assets/panel/js/turn/scehedule/sheduleSetting/workhours/workhours.js')); ?>"></script>
<script src="<?php echo e(asset('dr-assets/panel/js/bime/bime.js')); ?>"></script>
<script>
 var appointmentsSearchUrl = "<?php echo e(route('search.appointments')); ?>";
 var updateStatusAppointmentUrl =
  "<?php echo e(route('updateStatusAppointment', ':id')); ?>";
 $(function() {
  $('.card').css('width', '100%');
 });
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
</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('dr.panel.layouts.master', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\MyProjects\Benobe\panel\resources\views/dr/panel/bime/index.blade.php ENDPATH**/ ?>