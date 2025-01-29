@extends('dr.panel.layouts.master')
@section('styles')
 <style>
  .timepicker-ui-wrapper.mobile {
   direction: ltr !important;
  }

  input::placeholder {
   color: #c3c3c3 !important;
   font-size: 14px !important;
  }
 </style>
 <link type="text/css" href="{{ asset('dr-assets/panel/css/panel.css') }}" rel="stylesheet" />
 <link type="text/css" href="{{ asset('dr-assets/panel/css/turn/schedule/scheduleSetting/scheduleSetting.css') }}"
  rel="stylesheet" />
 <link type="text/css" href="{{ asset('dr-assets/panel/css/turn/schedule/scheduleSetting/workhours.css') }}">
 <link type="text/css" href="{{ asset('dr-assets/panel/profile/edit-profile.css') }}" rel="stylesheet" />

@endsection
@section('site-header')
 {{ 'به نوبه | پنل دکتر' }}
@endsection
@section('content')
@section('bread-crumb-title', ' اعلام مرخصی')
@include('dr.panel.my-tools.loader-btn')
<div class="blocking_users_content">
 <div class="container mt-4">
  <div class="card">
   <div class="card-header">
    <h5 class="mb-0">مدیریت کاربران مسدود</h5>
   </div>
   <div class="card-body">
    <!-- دکمه افزودن کاربر -->
    <div class="d-flex justify-content-between mb-3">
     <button class="h-50 btn btn-primary" data-toggle="modal" data-target="#addUserModal">افزودن کاربر مسدود</button>
    </div>

    <!-- جدول لیست کاربران مسدود -->
    <div class="table-responsive">
     <table id="blockedUsersTable" class="table table-striped table-bordered text-center">
      <thead>
       <tr>
        <th>نام کاربر</th>
        <th>شماره موبایل</th>
        <th>تاریخ شروع مسدودیت</th>
        <th>تاریخ پایان مسدودیت</th>
        <th>دلیل</th>
        <th>وضعیت</th>
        <th>عملیات</th>
       </tr>
      </thead>
      <tbody>
       @foreach ($blockedUsers as $index => $blockedUser)
        <tr data-id="{{ $blockedUser->id }}">
         <td>{{ $blockedUser->user->first_name }} {{ $blockedUser->user->last_name }}</td>
         <td>{{ $blockedUser->user->mobile }}</td>
         <td>{{ explode(' ', $blockedUser->blocked_at)[0] }}</td>
         <td>{{ explode(' ', $blockedUser->unblocked_at)[0] }}</td>
         <td>{{ $blockedUser->reason }}</td>
         <td>
          <span
           class="cursor-pointer font-weight-bold {{ $blockedUser->status == 1 ? 'text-danger' : 'text-success' }}"
           title="برای تغییر وضعیت کلیک کنید" data-toggle="tooltip" data-status="{{ $blockedUser->status }}"
           data-id="{{ $blockedUser->id }}" onclick="toggleStatus(this)">
           {{ $blockedUser->status == 1 ? 'مسدود' : 'آزاد' }}
          </span>
         </td>
         <td>
          <button class="rounded-circle btn btn-light btn-sm delete-user-btn">
           <img src="{{ asset('dr-assets/icons/trash.svg') }}" alt="Delete">
          </button>
         </td>
        </tr>
       @endforeach
      </tbody>
     </table>
    </div>
   </div>
  </div>
  <!-- جدول لیست پیام‌های ارسالی -->
  <div class="card mt-4">
   <div class="card-header">
    <h5 class="mb-0">لیست پیام‌های ارسالی</h5>
   </div>
   <div class="card-body">
    <div>
     <button class="h-50 btn btn-success" data-toggle="modal" data-target="#sendSmsModal">ارسال پیام</button>

    </div>
    <div class="table-responsive mt-3">
     <table id="messagesTable" class="table table-striped table-bordered text-center">
      <thead>
       <tr>
        <th>عنوان پیام</th>
        <th>متن پیام</th>
        <th>تاریخ ارسال</th>
        <th>گیرنده</th>
        <th>عملیات</th>
       </tr>
      </thead>
      <tbody id="messagesTableBody">
       @foreach ($messages as $index => $message)
        <tr data-id="{{ $message->id }}">
         <td>{{ $message->title }}</td>
         <td>{{ $message->content }}</td>
         <td>{{ \Morilog\Jalali\Jalalian::fromDateTime($message->created_at)->format('Y/m/d') }}</td>
         <td>{{ $message->user ? $message->user->first_name . ' ' . $message->user->last_name : 'نامشخص' }}</td>

         <td>
          <button class="btn btn-light btn-sm delete-message-btn" onclick="deleteMessage({{ $message->id }}, this)">
           <img src="{{ asset('dr-assets/icons/trash.svg') }}" alt="">
          </button>
         </td>
        </tr>
       @endforeach
      </tbody>

     </table>
    </div>
   </div>
  </div>

 </div>

 <!-- مودال افزودن کاربر -->
 <div class="modal fade" id="addUserModal" tabindex="-1" aria-labelledby="addUserModalLabel" aria-hidden="true">
  <div class="modal-dialog">
   <div class="modal-content border-radius-8">
    <div class="modal-header">
     <h5 class="modal-title" id="addUserModalLabel">افزودن کاربر مسدود</h5>
     <button type="button" class="close" data-dismiss="modal" aria-label="بستن">
      <span aria-hidden="true">&times;</span>
     </button>
    </div>
    <div class="modal-body">
     <form id="addUserForm" method="POST">
      @csrf
      <div class="form-group position-relative">
       <label class="label-top-input-special-takhasos" for="userId">موبایل</label>
       <input type="text" name="mobile" id="userMobile" class="form-control h-50 mb-3">
      </div>
      <div class="form-group position-relative">
       <label class="label-top-input-special-takhasos" for="startDate">تاریخ شروع مسدودیت</label>
       <input type="text" id="startDate" name="blocked_at" class="form-control h-50 mb-3" placeholder="1402/01/15">
      </div>
      <div class="form-group position-relative">
       <label class="label-top-input-special-takhasos" for="endDate">تاریخ پایان مسدودیت</label>
       <input type="text" id="endDate" name="unblocked_at" class="form-control h-50 mb-3" placeholder="1402/01/20">
      </div>
      <div class="form-group position-relative">
       <label class="label-top-input-special-takhasos" for="reason">دلیل مسدودیت</label>
       <textarea id="reason" name="reason" class="form-control h-50 mb-3" placeholder="دلیل مسدودیت را وارد کنید"></textarea>
      </div>
      <div class="mt-2 w-100">
       <button id="saveBlockedUserBtn" type="submit"
        class="btn btn-primary w-100 h-50 d-flex justify-content-center align-items-center">
        <span class="button_text">ثبت</span>
        <div class="loader"></div>
       </button>
      </div>
     </form>
    </div>

   </div>
  </div>
 </div>

 <!-- مودال ارسال پیام -->
<div class="modal fade" id="sendSmsModal" tabindex="-1" aria-labelledby="sendSmsModalLabel" aria-hidden="true">
 <div class="modal-dialog">
  <div class="modal-content border-radius-8">
   <div class="modal-header">
    <h5 class="modal-title" id="sendSmsModalLabel">ارسال پیام</h5>
    <button type="button" class="close" data-dismiss="modal" aria-label="بستن">
     <span aria-hidden="true">&times;</span>
    </button>
   </div>
   <div class="modal-body">
    <form id="sendSmsForm">
     @csrf
     <div class="form-group position-relative">
      <label class="label-top-input-special-takhasos" for="smsTitle">عنوان پیام</label>
      <input type="text" id="smsTitle" name="title" class="form-control h-50 mb-3" placeholder="عنوان پیام">
     </div>
     <div class="form-group position-relative">
      <label class="label-top-input-special-takhasos" for="smsMessage">متن پیام</label>
      <textarea id="smsMessage" name="content" class="form-control h-50 mb-3" rows="4" placeholder="متن پیام"></textarea>
     </div>
     <div class="form-group position-relative">
      <label class="label-top-input-special-takhasos" for="smsRecipient">گیرنده</label>
      <select id="smsRecipient" name="recipient_type" class="form-control h-50 mb-3">
       <option value="all">همه کاربران</option>
       <option value="blocked">کاربران مسدود</option>
       <option value="specific">کاربر خاص</option>
      </select>
     </div>
     <div class="form-group position-relative" id="specificRecipientField" style="display: none;">
      <label class="label-top-input-special-takhasos" for="specificRecipient">شماره موبایل گیرنده</label>
      <input type="text" id="specificRecipient" name="specific_recipient" class="form-control h-50 mb-3"
       placeholder="09123456789">
     </div>
     <div class="mt-2 w-100">
      <button type="submit" class="btn btn-primary w-100 h-50 d-flex justify-content-center align-items-center">
       <span class="button_text">ارسال</span>
       <div class="loader" style="display:none;"></div>
      </button>
     </div>
    </form>
   </div>
  </div>
 </div>
</div>



</div>
@endsection
@section('scripts')
<script src="{{ asset('dr-assets/panel/jalali-datepicker/run-jalali.js') }}"></script>
<script src="{{ asset('dr-assets/panel/js/dr-panel.js') }}"></script>
<script src="{{ asset('dr-assets/panel/js/turn/scehedule/sheduleSetting/workhours/workhours.js') }}"></script>
<script src="{{ asset('dr-assets/panel/js/turn/scehedule/sheduleSetting/vacation/vacation.js') }}"></script>
<script>
 $('#smsRecipient').on('change', function () {
  const specificField = $('#specificRecipientField');
  if ($(this).val() === 'specific') {
   specificField.show();
  } else {
   specificField.hide();
  }
 });

 $('#sendSmsForm').on('submit', function (e) {
   e.preventDefault();

   const form = $(this);
   const data = form.serialize();
   const button = form.find('button[type="submit"]');
   const loader = button.find('.loader');
   const buttonText = button.find('.button_text');

   button.prop('disabled', true);
   buttonText.hide();
   loader.show();

   $.ajax({
    url: "{{ route('doctor-blocking-users.send-message') }}",
    method: "POST",
    data: data,
    success: function (response) {
     if (response.success) {
      showToast(response.message, "success");
      const modal = $('#sendSmsModal');
      modal.modal('hide');
      modal.on('hidden.bs.modal', function () {
       $('body').removeClass('modal-open');
       $('.modal-backdrop').remove();
      });
      form[0].reset();
      loadMessages(); // بروزرسانی لیست پیام‌ها
     } else {
      showToast(response.message, "error");
     }
    },
    error: function (xhr) {
     if (xhr.status === 422) {
      const errorMessage = xhr.responseJSON.message || "خطای اعتبارسنجی رخ داده است.";
      showToast(errorMessage, "error");
     } else {
      showToast("خطا در ارسال پیام!", "error");
     }
    },
    complete: function () {
     button.prop('disabled', false);
     buttonText.show();
     loader.hide();
    }
   });
  });


</script>
<script>
 function appendBlockedUser(user) {
  const tableBody = $('#blockedUsersTable tbody');
  const rowCount = tableBody.find('tr').length;

  const statusText = user.status == 1 ? 'مسدود' : 'آزاد';
  const statusClass = user.status == 1 ? 'text-danger' : 'text-success';

  const newRow = `
        <tr data-id="${user.id}">
            <td>${user.user.first_name} ${user.user.last_name}</td>
            <td>${user.user.mobile}</td>
            <td>${user.blocked_at}</td>
            <td>${user.unblocked_at || '-'}</td>
            <td>${user.reason || ''}</td>
            <td>
                <span 
                    class="cursor-pointer font-weight-bold ${statusClass}"
                    data-toggle="tooltip"
                    data-status="${user.status}"
                    data-id="${user.id}"
                    onclick="toggleStatus(this)">
                    ${statusText}
                </span>
            </td>
            <td>
                <button class="rounded-circle btn btn-light btn-sm delete-user-btn">
                    <img src="{{ asset('dr-assets/icons/trash.svg') }}" alt="Delete">
                </button>
            </td>
        </tr>
    `;

  tableBody.append(newRow);
  $('[data-toggle="tooltip"]').tooltip();
 }



 $('#addUserForm').on('submit', function (e) {
   e.preventDefault();

   const form = $(this);
   const data = form.serialize();
   const button = form.find('button[type="submit"]');
   const loader = button.find('.loader');
   const buttonText = button.find('.button_text');

   button.prop('disabled', true);
   buttonText.hide();
   loader.show();

   $.ajax({
    url: "{{ route('doctor-blocking-users.store') }}",
    method: "POST",
    data: data,
    success: function (response) {
     if (response.success) {
      showToast(response.message, "success");
      appendBlockedUser(response.blocking_user); // ارسال داده به تابع
      form[0].reset();
        const modal = $('#addUserModal');
     modal.modal('hide');
     modal.on('hidden.bs.modal', function() {
      $('body').removeClass('modal-open');
      $('.modal-backdrop').remove();
     });
     }
    },
    error: function (xhr) {
     const errorMessage = xhr.responseJSON?.message || "خطا در ذخیره‌سازی!";
     showToast(errorMessage, "error");
    },
    complete: function () {
     button.prop('disabled', false);
     buttonText.show();
     loader.hide();
    }
   });
  });


 function showToast(message, type = "success") {
  const backgroundColor = type === "success" ? "green" : type === "error" ? "red" : "orange";
  Toastify({
   text: message || "عملیات با موفقیت انجام شد",
   duration: 3000,
   close: true,
   gravity: "top",
   position: "right",
   style: {
    background: backgroundColor,
    color: "white",
    fontSize: "14px",
   },
  }).showToast();
 }



 function loadBlockedUsers() {
  $.ajax({
   url: "{{ route('doctor-blocking-users.index') }}",
   method: "GET",
   success: function(response) {
    // به‌روزرسانی لیست کاربران مسدود
    console.log(response);

    const tableBody = $('#blockedUsersTable tbody');
    tableBody.empty();
    response.blockedUsers.forEach((user, index) => {

     tableBody.append(`
                    <tr data-id="${user.id}">
                        <td>${user.user.first_name} ${user.user.last_name}</td>
                        <td>${user.user.mobile}</td>
                        <td>${user.blocked_at}</td>
                        <td>${user.unblocked_at || '-'}</td>
                        <td>${user.reason || ''}</td>
                        <td>
                            <span 
                                class="cursor-pointer font-weight-bold  ${user.status == 1 ? 'text-danger' : 'text-success'}"
                                data-toggle="tooltip"
                                data-status="${user.status}"
                                data-id="${user.id}"
                                onclick="toggleStatus(this)">
                                ${user.status == 1 ? 'مسدود' : 'آزاد'}
                            </span>
                        </td>
                        <td>
                            <button class="rounded-circle btn btn-light btn-sm delete-user-btn">
                                <img src="{{ asset('dr-assets/icons/trash.svg') }}" alt="Delete">
                            </button>
                        </td>
                    </tr>
                `);
     $('[data-toggle="tooltip"]').tooltip();
    });
   },
   error: function() {
    showToast("خطا در بارگذاری لیست کاربران!", "error");
   }
  });
 }
 // تغییر از $(document).on('click', '.delete-user-btn', function (e) به این صورت:
 $(document).on('click', '#blockedUsersTable .delete-user-btn', function(e) {
  e.preventDefault();

  const row = $(this).closest('tr'); // پیدا کردن ردیف
  const userId = row.data('id'); // شناسه کاربر از خاصیت data-id

  if (!userId) {
   Swal.fire(
    'خطا!',
    'شناسه کاربر یافت نشد.',
    'error'
   );
   return;
  }

  Swal.fire({
   title: 'آیا مطمئن هستید؟',
   text: 'این کاربر برای همیشه حذف خواهد شد!',
   icon: 'warning',
   showCancelButton: true,
   confirmButtonColor: '#d33',
   cancelButtonColor: '#3085d6',
   confirmButtonText: 'بله، حذف شود!',
   cancelButtonText: 'لغو'
  }).then((result) => {
   if (result.isConfirmed) {
    // ارسال درخواست حذف با ایجکس
    $.ajax({
     url: "{{ route('doctor-blocking-users.destroy', ['id' => ':userId']) }}".replace(':userId', userId),
     method: 'DELETE',
     data: {
      _token: '{{ csrf_token() }}', // توکن CSRF
     },
     success: function(response) {
      if (response.success) {
       Swal.fire(
        'حذف شد!',
        response.message || 'کاربر با موفقیت حذف شد.',
        'success'
       );
       // حذف ردیف از جدول
       row.remove();
      } else {
       Swal.fire(
        'خطا!',
        response.message || 'خطا در حذف کاربر.',
        'error'
       );
      }
     },
     error: function(xhr) {
      Swal.fire(
       'خطا!',
       xhr.responseJSON?.message || 'خطایی رخ داده است.',
       'error'
      );
     }
    });
   }
  });
 });





 // بارگذاری پیام‌ها
 function loadMessages() {
   $.ajax({
    url: "{{ route('doctor-blocking-users.messages') }}",
    method: "GET",
    success: function (messages) {
     const tableBody = $('#messagesTableBody');
     tableBody.empty();

     messages.forEach((message, index) => {
      const recipientName = message.user
       ? `${message.user.first_name} ${message.user.last_name}`
       : 'نامشخص';

      const jalaliDate = new Intl.DateTimeFormat('fa-IR', {
       year: 'numeric',
       month: '2-digit',
       day: '2-digit'
      }).format(new Date(message.created_at));

      tableBody.append(`
                    <tr data-id="${message.id}">
                        <td>${message.title}</td>
                        <td>${message.content}</td>
                        <td>${jalaliDate}</td>
                        <td>${recipientName}</td>
                        <td>
                            <button class="btn btn-light btn-sm delete-message-btn" onclick="deleteMessage(${message.id}, this)">
                                <img src="{{ asset('dr-assets/icons/trash.svg') }}" alt="Delete">
                            </button>
                        </td>
                    </tr>
                `);
     });
    },
    error: function () {
     showToast("خطا در بارگذاری پیام‌ها!", "error");
    }
   });
  }
 loadMessages()
 // بارگذاری اولیه پیام‌ها
 // فعال‌سازی تولتیپ بوت‌استرپ
 $(document).ready(function() {
  $('[data-toggle="tooltip"]').tooltip();
 });

 // تغییر وضعیت مسدودی کاربر
 function toggleStatus(element) {
   const userId = $(element).data('id');
   const currentStatus = $(element).data('status');
   const newStatus = currentStatus === 1 ? 0 : 1;
   const statusText = newStatus === 1 ? 'مسدود' : 'آزاد';
   const confirmationMessage = `آیا مطمئن هستید که می‌خواهید وضعیت این کاربر را به "${statusText}" تغییر دهید؟`;

   // نمایش دیالوگ SweetAlert
   Swal.fire({
    title: 'تغییر وضعیت',
    text: confirmationMessage,
    icon: 'warning',
    showCancelButton: true,
    confirmButtonText: 'بله، تغییر بده',
    cancelButtonText: 'لغو',
    confirmButtonColor: '#d33',
    cancelButtonColor: '#3085d6',
   }).then((result) => {
    if (result.isConfirmed) {
     // ارسال درخواست AJAX برای تغییر وضعیت
     $.ajax({
      url: "{{ route('doctor-blocking-users.update-status') }}",
      method: "PATCH",
      data: {
       _token: '{{ csrf_token() }}',
       id: userId,
       status: newStatus,
      },
      success: function (response) {
       if (response.success) {
        // تغییر متن وضعیت
        $(element)
         .removeClass('text-danger text-success')
         .addClass(newStatus === 1 ? 'text-danger' : 'text-success')
         .text(statusText);
        $(element).data('status', newStatus);

        showToast(response.message, 'success');

        // بروزرسانی لیست پیام‌ها
        loadMessages();
       } else {
        showToast(response.message, 'error');
       }
      },
      error: function () {
       showToast('خطا در تغییر وضعیت.', 'error');
      },
     });
    }
   });
  }


 function deleteMessage(messageId, element) {
  Swal.fire({
   title: 'آیا مطمئن هستید؟',
   text: 'این پیام برای همیشه حذف خواهد شد!',
   icon: 'warning',
   showCancelButton: true,
   confirmButtonText: 'بله، حذف کن',
   cancelButtonText: 'لغو',
   confirmButtonColor: '#d33',
   cancelButtonColor: '#3085d6',
  }).then((result) => {
   if (result.isConfirmed) {
    // ارسال درخواست حذف پیام
    $.ajax({
     url: "{{ route('doctor-blocking-users.delete-message', '') }}/" + messageId,
     method: "DELETE",
     data: {
      _token: "{{ csrf_token() }}"
     },
     success: function(response) {
      if (response.success) {
       // حذف سطر از جدول
       $(element).closest('tr').remove();

       // نمایش پیام موفقیت
       showToast('پیام با موفقیت حذف شد.', 'success');
      } else {
       showToast('خطا در حذف پیام.', 'error');
      }
     },
     error: function() {
      showToast('خطا در ارتباط با سرور.', 'error');
     }
    });
   }
  });
 }
</script>

@endsection
