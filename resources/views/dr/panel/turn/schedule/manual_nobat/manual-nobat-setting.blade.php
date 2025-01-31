@extends('dr.panel.layouts.master')
@section('styles')
 <link type="text/css" href="{{ asset('dr-assets/panel/css/panel.css') }}" rel="stylesheet" />
 <link type="text/css" href="{{ asset('dr-assets/panel/profile/edit-profile.css') }}" rel="stylesheet" />
 <link type="text/css" href="{{ asset('dr-assets/panel/css/profile/subuser.css') }}" rel="stylesheet" />
 <link type="text/css" href="{{ asset('dr-assets/panel/css/turn/schedule/scheduleSetting/workhours.css') }}"
  rel="stylesheet" />
 <link type="text/css" href="{{ asset('dr-assets/panel/css/turn/schedule/appointments_open/appointments_open.css') }}"
  rel="stylesheet" />
 <link type="text/css" href="{{ asset('dr-assets/panel/turn/schedule/manual_nobat/manual_nobat.css') }}"
  rel="stylesheet" />
@endsection
@section('site-header')
 {{ 'به نوبه | پنل دکتر' }}
@endsection
@section('content')
@section('bread-crumb-title', ' تنظیمات نوبت دستی')
@include('dr.panel.my-tools.loader-btn')
<div class="manual-nobat-content w-100 d-flex justify-content-center mt-3">
 <div class="manual-nobat-content-wrapper p-3">
  <div class="main-content">
   <div class="row no-gutters font-size-13 margin-bottom-10">
    <div class="user-panel-content w-100">
     <div class="p-3 w-100 d-flex justify-content-center">
      <div class="card clrfix" style="width: 850px;height: 100%;">
       <div class="card-header"> تنظیمات تایید دو مرحله ای نوبت‌های دستی</div>
       <div class="card-body">
        <div class="alert alert-info">
         <i class="fa fa-info-circle"></i>
         <strong>راهنما!</strong>
         <div>
          در فیلد اول میتوانید مشخص کنید که چند ساعت قبل از زمان نوبت پیامک تایید نهایی نوبت ارسال شود و در فیلد
          دوم، می‌توانید مشخص کنید بیمار چند ساعت مهلت دارد نوبت خود را تایید کند، در غیر اینصورت نوبت لغو خواهد
          شد.<br>
          در زیر با استفاده از گزینه بلی یا خیر میتوانید این امکان را فعال یا غیرفعال نمایید.
         </div>
        </div>
        <form method="post" action="" autocomplete="off" id="save_verify_nobat">
         <div class="row">
          <div class="col-md-6 col-sm-12 col-12">
           <div class="mt-3 position-relative">
            <label>آیا تایید دو مرحله‌ای نوبت‌های دستی فعال باشد؟</label>
            <select class="form-control h-50" name="status">
             <option value="0">خیر</option>
             <option value="1" selected="">بلی</option>
            </select>
           </div>
          </div>
          <div class="col-12 col-md-12 col-sm-12"></div>
          <div class="col-md-6 col-sm-12 col-12 stmvd" style="display: block">
           <div class="mt-3 position-relative">
            <label class="label-top-input-special-takhasos">زمان ارسال لینک تایید:</label>
            <div class="input-group">
             <input class="form-control ltr center h-50 border-radius-0" type="tel" value="3"
              name="duration_send_link" placeholder="مثلا: 72">
             <div class="input-group-append"><span class="input-group-text">ساعت قبل</span></div>
            </div>
           </div>
          </div>
          <div class="col-12"></div>
          <div class="col-md-6 col-sm-12 col-12 stmvd" style="display: block">
           <div class="mt-3 position-relative">
            <label class="label-top-input-special-takhasos">مدت زمان اعتبار لینک:</label>
            <div class="input-group">
             <input class="form-control ltr center h-50 border-radius-0" type="tel" value="1"
              name="duration_confirm_link" placeholder="مثلا: 48">
             <div class="input-group-append"><span class="input-group-text">ساعت</span></div>
            </div>
           </div>
          </div>
         </div>
         <div class="mt-3 position-relative p-3"><button type="submit"
          class="w-100 btn btn-primary h-50 border-radius-4 d-flex justify-content-center align-items-center">
          <span class="button_text">ذخیره تغیرات</span>
          <div class="loader"></div>
        </button> </div>
        </form>
       </div>
      </div>
     </div>
    </div>
   </div>
  </div>
 </div>
</div>
@endsection
@section('scripts')
<script src="{{ asset('dr-assets/panel/jalali-datepicker/run-jalali.js') }}"></script>
<script src="{{ asset('dr-assets/panel/js/turn/scehedule/sheduleSetting/workhours/workhours.js') }}"></script>
<script src="{{ asset('dr-assets/panel/js/dr-panel.js') }}"></script>
<script>

 var appointmentsSearchUrl = "{{ route('search.appointments') }}";
 var updateStatusAppointmentUrl =
  "{{ route('updateStatusAppointment', ':id') }}";
</script>
<script>

  $(document).ready(function () {
    $('#save_verify_nobat').on('submit', function (e) {
      e.preventDefault();

      const form = $(this);
      const submitButton = form.find('button[type="submit"]');
      const loader = $('<span class="spinner-border spinner-border-sm ml-2" role="status" aria-hidden="true"></span>');
      const buttonText = submitButton.html();

      // افزودن لودینگ به دکمه
      submitButton.prop('disabled', true).append(loader);

      $.ajax({
        url: "{{ route('manual-nobat.settings.save') }}", // به جای این، روت مناسب خود را قرار دهید
        method: 'POST',
        data: form.serialize(),
         headers: {
          'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        success: function (response) {
          if (response.success) {
            toastr.success(response.message || 'تغییرات با موفقیت ذخیره شد!');
          } else {
            toastr.error(response.message || 'خطا در ذخیره تغییرات!');

          }
        },
        error: function () {
            toastr.error('خطا در ارتباط با سرور!');

        },
        complete: function () {
          // بازگرداندن حالت اولیه دکمه
          submitButton.prop('disabled', false).html(buttonText);
        },
      });
    });

    
  });

</script>
@endsection
