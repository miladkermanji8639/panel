
<?php $__env->startSection('styles'); ?>
   <link type="text/css" href="<?php echo e(asset('dr-assets/panel/profile/edit-profile.css')); ?>" rel="stylesheet" />
   <link type="text/css" href="<?php echo e(asset('dr-assets/panel/css/panel.css')); ?>" rel="stylesheet" />
   <link type="text/css" href="<?php echo e(asset('dr-assets/panel/tickets/tickets.css')); ?>" rel="stylesheet" />
    <style>
    .myPanelOption {
      display: none;
    }
    </style>
<?php $__env->stopSection(); ?>
<?php $__env->startSection('site-header'); ?>
 <?php echo e('به نوبه | پنل دکتر'); ?>

<?php $__env->stopSection(); ?>
<?php $__env->startSection('content'); ?>
<?php $__env->startSection('bread-crumb-title', ' مشاهده پاسخ تیکت '); ?>
<?php $__env->startSection('content'); ?>
 <div class="container mt-4">
  <div class="card shadow border-0">
   <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
    <h5 class="mb-0">جزئیات تیکت #<?php echo e($ticket->id); ?></h5>
    <a href="<?php echo e(route('dr-panel-tickets')); ?>" class="btn btn-light btn-sm">
     بازگشت
    </a>
   </div>

   <div class="card-body border-radius-4">
    <h5 class="text-dark font-weight-bold">اطلاعات تیکت</h5>

    <!-- جدول نمایش اطلاعات تیکت -->
    <div class="table-responsive mt-3">
     <table class="table table-bordered">
      <tbody>
       <tr>
        <th class="bg-light">عنوان تیکت</th>
        <td><?php echo e($ticket->title); ?></td>
       </tr>
       <tr>
        <th class="bg-light">متن تیکت</th>
        <td><?php echo e($ticket->description); ?></td>
       </tr>
       <tr>
        <th class="bg-light">وضعیت</th>
        <td>
         <span
          class="badge badge-lg   
            <?php if($ticket->status == 'open'): ?> badge-success   
            <?php elseif($ticket->status == 'pending'): ?> badge-warning   
           <?php elseif($ticket->status == 'closed'): ?> badge-danger  
           <?php elseif($ticket->status == 'answered'): ?> badge-info   
          <?php else: ?> badge-secondary <?php endif; ?>">
          <?php if($ticket->status == 'open'): ?>
           باز
          <?php elseif($ticket->status == 'pending'): ?>
           در انتظار پا
           سخ
          <?php elseif($ticket->status == 'closed'): ?>
           بسته ش
           ده
          <?php elseif($ticket->status == 'answered'): ?>
           پاسخ داده ش
           ده
          <?php else: ?>
           نامشخص
          <?php endif; ?>
         </span>
        </td>
       </tr>
       <tr>
        <th class="bg-light">تاریخ ایجاد</th>
        <td><?php echo e(\Morilog\Jalali\Jalalian::forge($ticket->created_at)->format('Y/m/d - H:i')); ?></td>
       </tr>
      </tbody>
     </table>
    </div>

    <h5 class="mt-4">پاسخ‌ها</h5>
    <div class="response-list mt-3">
     <?php $__empty_1 = true; $__currentLoopData = $ticket->responses; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $response): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
      <div class="response-card p-3 border rounded mb-2 bg-light">
       <strong>
        <?php echo e($response->doctor ? 'دکتر ' . $response->doctor->first_name . ' ' . $response->doctor->last_name : 'نامشخص'); ?>

       </strong>
       <p class="mb-1"><?php echo e($response->message); ?></p>
       <small class="text-muted">
        <?php echo e(\Morilog\Jalali\Jalalian::forge($response->created_at)->ago()); ?>

       </small>
      </div>
     <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
      <div class="alert alert-info mt-3">هیچ پاسخی برای این تیکت ثبت نشده است.</div>
     <?php endif; ?>

    </div>


    <!-- فرم ارسال پاسخ -->
    <form id="add-response-form" class="mt-3">
     <?php echo csrf_field(); ?>
     <input type="hidden" id="ticket-id" value="<?php echo e($ticket->id); ?>">
     <div class="form-group">
      <label for="response-message">ارسال پاسخ</label>
      <textarea class="form-control" id="response-message" rows="3" placeholder="پاسخ خود را وارد کنید"></textarea>
     </div>

     <button type="submit" class="btn btn-primary h-50 col-12 d-flex justify-content-center align-items-center"
      id="save-response" <?php if($ticket->status == 'closed'): ?> disabled <?php endif; ?>>
      <span class="button_text">پاسخ تیکت</span>
      <div class="loader"></div>
     </button>

     <!-- پیام هشدار در صورت بسته بودن تیکت -->
     <?php if($ticket->status == 'closed'): ?>
      <div class="alert alert-warning mt-2">
       این تیکت بسته شده است و امکان ارسال پاسخ وجود ندارد.
      </div>
     <?php endif; ?>
    </form>

   </div>
  </div>

 </div>
<?php $__env->stopSection(); ?>
<?php $__env->startSection('scripts'); ?>
 <script src="<?php echo e(asset('dr-assets/panel/jalali-datepicker/run-jalali.js')); ?>"></script>

 <script src="<?php echo e(asset('dr-assets/panel/js/dr-panel.js')); ?>"></script>
 <script>
  var appointmentsSearchUrl = "<?php echo e(route('search.appointments')); ?>";
  var updateStatusAppointmentUrl =
   "<?php echo e(route('updateStatusAppointment', ':id')); ?>";
 </script>
 <script>
  $(document).ready(function() {
   $('#add-response-form').on('submit', function(e) {
    e.preventDefault();
    let ticketId = $('#ticket-id').val();
    let message = $('#response-message').val();
    let button = $(this).find('button');
    let loader = button.find('.loader');
    const buttonText = button.find('.button_text');

    // بررسی وضعیت تیکت و جلوگیری از ارسال درخواست
    if (button.is(':disabled')) {
     toastr.warning('این تیکت بسته شده است و نمی‌توانید پاسخ ارسال کنید!');
     return;
    }

    // نمایش لودر
    buttonText.hide();
    loader.show();

    // بررسی مقدار خالی و نمایش خطا
    if (message.trim() === '') {
     toastr.error('لطفاً متن پاسخ را وارد کنید!');
     buttonText.show();
     loader.hide();
     return;
    }

    $.ajax({
     url: "<?php echo e(route('dr-panel-tickets.responses.store', ':id')); ?>".replace(':id', ticketId),
     method: "POST",
     data: {
      _token: "<?php echo e(csrf_token()); ?>",
      message: message
     },
     success: function(response) {
      $('#response-message').val('');
      $('.response-list').append(`
                    <div class="response-card p-3 border rounded mb-2 bg-light">
                        <strong>${response.user}</strong>
                        <p class="mb-1">${response.message}</p>
                        <small class="text-muted">${response.created_at}</small>
                    </div>
                `);
      toastr.success("پاسخ شما ارسال شد!");
     },
     error: function() {
      toastr.error("خطا در ارسال پاسخ!");
     },
     complete: function() {
      buttonText.show();
      loader.hide();
     }
    });
   });
  });
 </script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('dr.panel.layouts.master', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\MyProjects\Benobe\panel\resources\views/dr/panel/tickets/show.blade.php ENDPATH**/ ?>