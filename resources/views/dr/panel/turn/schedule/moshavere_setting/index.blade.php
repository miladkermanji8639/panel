@extends('dr.panel.layouts.master')

@section('styles')
 <link type="text/css" href="{{ asset('dr-assets/panel/css/panel.css') }}" rel="stylesheet" />
 <link type="text/css" href="{{ asset('dr-assets/panel/css/turn/schedule/scheduleSetting/scheduleSetting.css') }}"
  rel="stylesheet" />
 <link type="text/css" href="{{ asset('dr-assets/panel/profile/edit-profile.css') }}" rel="stylesheet" />
 <link type="text/css" href="{{ asset('dr-assets/panel/css/turn/schedule/scheduleSetting/workhours.css') }}"
  rel="stylesheet" />
@endsection

@section('site-header')
 {{ 'به نوبه | پنل دکتر' }}
@endsection

@section('content')
@section('bread-crumb-title', 'برنامه ریزی مشاوره آنلاین')
<div class="w-100 d-flex justify-content-center" dir="ltr">
 <div class="auto-scheule-content-top">
  <x-my-toggle-appointment :isChecked="$appointmentConfig->auto_scheduling" id="appointment-toggle" day="مشاوره آنلاین" class="mt-3" />
 </div>
</div>
<div class="workhours-content w-100 d-flex justify-content-center mt-4 ">

 <div class="workhours-wrapper-content p-3 {{ $appointmentConfig->auto_scheduling ? '' : 'd-none' }}">
  <div>
   <div>
    <div>
     <div>
      <div class="input-group position-relative mx-2">
       <label class="label-top-input-special-takhasos"> تعداد روز های باز تقویم </label>
       <input type="number" value="" class="form-control text-center h-50 border-radius-0" name="calendar_days"
        placeholder="تعداد روز مورد نظر خود را وارد کنید">
       <div class="input-group-append count-span-prepand-style"><span class="input-group-text px-2">روز</span>
       </div>
      </div>
      <div class="d-flex justify-content-end w-100 mt-2">
       <div class="my-tooltip mx-2 position-absolute">
        <svg data-toggle="tooltip" data-placement="bottom" title="" width="16" height="17"
         viewBox="0 0 16 17" fill="none" xmlns="http://www.w3.org/2000/svg" class="hidden lg:block svg-help"
         color="#3f4079" data-tip="true" data-for="centerSelect" currentitem="false"
         data-original-title=" توجه! مبلغ هر دقیقه مکالمه در بسته های زیر پورسانت سایت میباشد 1,000 تومان
به طور مثال ۱۰ دقیقه مکامله با نرخ ۵۰۰ تومان میشود پنج هزارتومان که از هر مشاوره شما کسر و دریافت میگردد
چنانچه از ۱۰ دقیقه مشاوره 6 دقیقه مکالمه انجام شود مبلغ پورسانت سایت بصورت کامل برداشت میشود">
         <path
          d="M8.00006 9.9198V9.70984C8.00006 9.02984 8.42009 8.66982 8.84009 8.37982C9.25009 8.09982 9.66003 7.73983 9.66003 7.07983C9.66003 6.15983 8.92006 5.4198 8.00006 5.4198C7.08006 5.4198 6.34009 6.15983 6.34009 7.07983"
          stroke="#3f4079" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"></path>
         <path d="M7.9955 12.0692H8.0045" stroke="#3f4079" stroke-width="1.5" stroke-linecap="round"
          stroke-linejoin="round">
         </path>
         <circle cx="8" cy="8.99445" r="7.25" stroke="#3f4079" stroke-width="1.5"></circle>
        </svg>
       </div>
      </div>
      <div class="form-group w-100 mt-3 pb-4">
       <label class="text-dark font-weight-bold">تعرفه مشاوره</label>

       <div class="row mt-3 w-100">
        <div class="col-md-6">
         <div class="input-group position-relative">
          <label class="label-top-input-special-takhasos">15 دقیقه</label>
          <input type="tel" value="{{  explode(".", $appointmentConfig->price_15min)[0] }}" class="form-control numberkey ltr text-center h-50 border-radius-0"
           name="call_15min_1">
          <div class="input-group-append"><span class="input-group-text">تومان</span></div>
         </div>
        </div>
        <div class="col-md-6 therty-min-768">
         <div class="input-group position-relative">
          <label class="label-top-input-special-takhasos">30 دقیقه</label>
          <input type="tel" value="{{  explode(".", $appointmentConfig->price_30min)[0] }}" class="form-control numberkey ltr text-center h-50 border-radius-0"
           name="call_15min_2">
          <div class="input-group-append"><span class="input-group-text">تومان</span></div>
         </div>
        </div>
       </div>

       <div class="row mt-3">
        <div class="col-md-6">
         <div class="input-group position-relative">
          <label class="label-top-input-special-takhasos">45 دقیقه</label>
          <input type="tel" value="{{  explode(".", $appointmentConfig->price_45min)[0] }}" class="form-control numberkey ltr text-center h-50 border-radius-0"
           name="call_15min_3">
          <div class="input-group-append"><span class="input-group-text">تومان</span></div>
         </div>
        </div>
        <div class="col-md-6 sixty-min-768">
         <div class="input-group position-relative">
          <label class="label-top-input-special-takhasos">60 دقیقه</label>
          <input type="tel" value="{{  explode(".", $appointmentConfig->price_60min)[0] }}" class="form-control numberkey ltr text-center h-50 border-radius-0"
           name="call_15min_4">
          <div class="input-group-append"><span class="input-group-text">تومان</span></div>
         </div>
        </div>
       </div>

      </div>
      <div class="mt-2">
       <label class="text-dark font-weight-bold">روزهای کاری</label>
       <div class="d-flex flex-wrap justify-content-start mt-3 gap-40">
        <x-my-check :isChecked="false" id="saturday" day="شنبه" />
        <x-my-check :isChecked="false" id="sunday" day="یکشنبه" />
        <x-my-check :isChecked="false" id="monday" day="دوشنبه" />
        <x-my-check :isChecked="false" id="tuesday" day="سه‌شنبه" />
        <x-my-check :isChecked="false" id="wednesday" day="چهارشنبه" />
        <x-my-check :isChecked="false" id="thursday" day="پنج‌شنبه" />
        <x-my-check :isChecked="false" id="friday" day="جمعه" />
       </div>
       <div id="work-hours" class="mt-4">
       </div>
      </div>
     </div>
     <div class="mt-5">
      <x-my-check :isChecked="$appointmentConfig->online_consultation" id="posible-appointments"
       day="امکان دریافت مشاوره آنلاین توسط کاربران وجود داشته باشد؟" />
     </div>
     <div class="mt-3">
      <x-my-check :isChecked="$appointmentConfig->holiday_availability" id="posible-appointments-inholiday" day="مشاوره آنلاین تعطیلات رسمی" />
     </div>
    </div>
    <div class="d-flex w-100 justify-content-end mt-3">
     <button type="submit" class="btn btn-primary h-50 col-12 d-flex justify-content-center align-items-center"
      id="save-work-schedule">
      <span class="button_text">ذخیره تغیرات</span>
      <div class="loader"></div>
     </button>
    </div>
    <hr>
    @if (isset($_GET['activation-path']) && $_GET['activation-path'] == true)
     <div class="w-100">
      <button class="btn btn-outline-primary w-100 h-50" tabindex="0" type="button" id=":rs:"
       data-toggle="modal" data-target="#activation-modal">پایان فعالسازی<span></span></button>
     </div>
     <div class="modal fade" id="activation-modal" tabindex="-1" role="dialog"
      aria-labelledby="activation-modal-label" aria-hidden="true">
      <div class="modal-dialog modal-dialog-centered" role="document">
       <div class="modal-content border-radius-8">
        <div class="modal-header">
         <h5 class="modal-title" id="activation-modal-label">فعالسازی نوبت دهی</h5>
         <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
         </button>
        </div>
        <div class="modal-body">
         <div>
          <p>اطلاعات شما ثبت شد و ویزیت آنلاین شما تا ساعاتی دیگر فعال می‌شود. بیماران می‌توانند مستقیماً از طریق
           پروفایل شما ویزیت آنلاین رزرو کنند.</p>
          <p>به دلیل محدودیت ظرفیت فعلی، نمایه شما در ابتدا در لیست پزشکان موجود برای ویزیت آنلاین در رتبه
           پایین‌تری
           قرار می‌گیرد.</p>
          <p>برای هر گونه سوال یا توضیح بیشتر، لطفا با ما <a style="color: blue"
            href="https://newsupport.paziresh24.com/new-ticket/?department=4&amp;product=9">ارتباط</a> بگیرید.
           تیم ما
           اینجاست تا از شما در هر مرحله حمایت کند.</p>
         </div>
        </div>
        <div class="p-3">
         <a href="{{ route('dr-panel', ['showModal' => 'true']) }}"
          class="btn btn-primary w-100 h-50 d-flex align-items-center text-white justify-content-center">شروع نوبت
          دهی</a>
        </div>
       </div>
      </div>
     </div>
    @endif
   </div>
  </div>
 </div>
</div>
@include('dr.panel.my-tools.counseling')
@endsection

@section('scripts')
<script src="{{ asset('dr-assets/panel/jalali-datepicker/run-jalali.js') }}"></script>
<script src="{{ asset('dr-assets/panel/js/dr-panel.js') }}"></script>
<script src="{{ asset('dr-assets/panel/js/turn/scehedule/sheduleSetting/workhours/workhours.js') }}"></script>
<script>
 var appointmentsSearchUrl = "{{ route('search.appointments') }}";
 var updateStatusAppointmentUrl = "{{ route('updateStatusAppointment', ':id') }}";
 jalaliDatepicker.startWatch();
 var svgUrl = "{{ asset('dr-assets/icons/copy.svg') }}";
 var trashSvg = "{{ asset('dr-assets/icons/trash.svg') }}";
</script>
@endsection
