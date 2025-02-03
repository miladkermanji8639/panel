@extends('dr.panel.layouts.master')
@section('styles')
 <link type="text/css" href="{{ asset('dr-assets/panel/css/panel.css') }}" rel="stylesheet" />
 <link type="text/css" href="{{ asset('dr-assets/panel/css/turn/schedule/schedule-setting/my-special-days.css') }}"
  rel="stylesheet" />
@endsection
@section('site-header')
 {{ 'به نوبه | پنل دکتر' }}
@endsection
@section('content')
@section('bread-crumb-title', 'تعطیلات و نوبت دهی روز های خاص')
<div class="container calendar mt-2">
 <div class="calendar-header w-100 d-flex justify-content-between align-items-center gap-4">
  <div class="">
   <button id="prev-month" class="btn btn-light">
    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
     <g id="Arrow / Chevron_Right_MD">
      <path id="Vector" d="M10 8L14 12L10 16" stroke="#000000" stroke-width="2" stroke-linecap="round"
       stroke-linejoin="round" />
     </g>
    </svg>
   </button>
  </div>
  <div class="w-100">
   <select id="year" class="form-select w-100 bg-light border-0"></select>
  </div>
  <div class="w-100">
   <select id="month" class="form-select w-100 bg-light border-0"></select>
  </div>
  <div class="">
   <button id="next-month" class="btn btn-light"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
     viewBox="0 0 24 24" fill="none">
     <g id="Arrow / Chevron_Left_MD">
      <path id="Vector" d="M14 16L10 12L14 8" stroke="#000000" stroke-width="2" stroke-linecap="round"
       stroke-linejoin="round" />
     </g>
    </svg>
   </button>
  </div>
 </div>
 <div class="calendar-body calendar-body-g-425"> <!-- عناوین روزهای هفته -->
  <div class="calendar-day-name text-center">شنبه</div>
  <div class="calendar-day-name text-center">یک‌شنبه</div>
  <div class="calendar-day-name text-center">دوشنبه</div>
  <div class="calendar-day-name text-center">سه‌شنبه</div>
  <div class="calendar-day-name text-center">چهارشنبه</div>
  <div class="calendar-day-name text-center">پنج‌شنبه</div>
  <div class="calendar-day-name text-center">جمعه</div>
 </div>
 <div class="calendar-body-425 d-none"> <!-- عناوین روزهای هفته -->
  <div class="calendar-day-name text-center">ش</div>
  <div class="calendar-day-name text-center">ی</div>
  <div class="calendar-day-name text-center">د</div>
  <div class="calendar-day-name text-center">س</div>
  <div class="calendar-day-name text-center">چ</div>
  <div class="calendar-day-name text-center">پ</div>
  <div class="calendar-day-name text-center">ج</div>
 </div>
 <div class="calendar-body" id="calendar-body"> <!-- تقویم در اینجا بارگذاری می‌شود --> </div>
</div> <!-- Modal -->
<div class="modal fade" id="dateModal" tabindex="-1" aria-labelledby="dateModalLabel" aria-hidden="true">
 <div class="modal-dialog modal-dialog-centered">

  <div class="modal-content border-radius-8">

   <div class="modal-header">
    <h6 class="modal-title" id="dateModalLabel">تاریخ</h6>
    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
     <span aria-hidden="true">&times;</span>
    </button>
   </div>

   <div class="modal-body">
    
   </div>
  </div>
 </div>
</div>
<!-- جابجایی نوبت Modal -->
<div class="modal  fade" id="rescheduleModal" tabindex="-1" aria-labelledby="rescheduleModalLabel" aria-hidden="true">
 <div class="modal-dialog modal-dialog-centered modal-lg">
  <div class="modal-content border-radius-8">
   <div class="modal-header">
    <h6 class="modal-title" id="rescheduleModalLabel">جابجایی نوبت</h6>
    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
     <span aria-hidden="true">&times;</span>
    </button>
   </div>
   <div class="modal-body">
    <p class="font-weight-bold">لطفا یک روز جدید را انتخاب کنید:</p>
    <div class="calendar-header w-100 d-flex justify-content-between align-items-center gap-4">
     <div class="">
      <button id="prev-month-reschedule" class="btn btn-light">
       <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none">
        <g id="Arrow / Chevron_Right_MD">
         <path id="Vector" d="M10 8L14 12L10 16" stroke="#000000" stroke-width="2" stroke-linecap="round"
          stroke-linejoin="round" />
        </g>
       </svg>
      </button>
     </div>
     <div class="w-100">
      <select id="year-reschedule" class="form-select w-100 bg-light border-0"></select>
     </div>
     <div class="w-100">
      <select id="month-reschedule" class="form-select w-100 bg-light border-0"></select>
     </div>
     <div class="">
      <button id="next-month-reschedule" class="btn btn-light"><svg xmlns="http://www.w3.org/2000/svg"
        width="24" height="24" viewBox="0 0 24 24" fill="none">
        <g id="Arrow / Chevron_Left_MD">
         <path id="Vector" d="M14 16L10 12L14 8" stroke="#000000" stroke-width="2" stroke-linecap="round"
          stroke-linejoin="round" />
        </g>
       </svg>
      </button>
     </div>
    </div>
    <div class="w-100 d-flex justify-content-end">
     <button id="goToFirstAvailable" class="btn btn-light w-100 border">برو به اولین نوبت خالی</button>
    </div>
    <div id="calendar-reschedule" class="calendar-body"></div>
    <div class="w-100 d-flex justify-content-between gap-4 mt-3">
     <button type="button" class="btn btn-primary h-50 w-100" id="confirmReschedule">تأیید</button>
     <button type="button" class="btn btn-danger h-50 w-100 close-modal" class="close" data-dismiss="modal"
      aria-label="Close">انصراف</button>
    </div>
   </div>
  </div>
 </div>
</div>
@endsection
@section('scripts')
<script src="{{ asset('dr-assets/panel/jalali-datepicker/run-jalali.js') }}"></script>
<script src="{{ asset('dr-assets/panel/js/dr-panel.js') }}"></script>
<script>
 var appointmentsSearchUrl = "{{ route('search.appointments') }}";
 var updateStatusAppointmentUrl =
  "{{ route('updateStatusAppointment', ':id') }}";
</script>
<script>
 document.addEventListener('DOMContentLoaded', function() {
  const urlParams = new URLSearchParams(window.location.search);
  if (urlParams.has('showModal')) {
   // فرض کنید ID مودال شما "activation-modal" است
   $('#activation-modal').modal('show');
  }
 });
</script>
<script src="https://cdn.jsdelivr.net/npm/jalali-moment/dist/jalali-moment.browser.js"></script>
<script>
 let selectedDay = null;

 function generateCalendar(year, month) {
  const calendarBody = $('#calendar-body');
  calendarBody.empty(); // پاک کردن محتوای قبلی تقویم
  const today = moment().startOf('day').locale('fa');
  const firstDayOfMonth = moment(`${year}-${month}-01`, 'jYYYY-jMM-jDD').locale('fa').startOf('month');
  const daysInMonth = firstDayOfMonth.jDaysInMonth();
  let firstDayWeekday = firstDayOfMonth.weekday();
  for (let i = 0; i < firstDayWeekday; i++) {
   calendarBody.append('<div class="calendar-day empty"></div>');
  }
  for (let day = 1; day <= daysInMonth; day++) {
   const currentDay = firstDayOfMonth.clone().add(day - 1, 'days');
   const dayClass = `calendar-day ${currentDay.isSame(today, 'day') ? 'active' : ''}`;
   const dayElement = `
            <div class="${dayClass} position-relative" data-date="${currentDay.format('jYYYY-jMM-jDD')}">
                <span>${currentDay.format('jD')} <span class="d-none-425">${currentDay.format('jMMMM')}</span></span>
            </div>`;
   calendarBody.append(dayElement);
  }
  attachDayClickEvents();
  loadAppointmentsCount();
  loadHolidayStyles();
 }
  function updateWorkhours() {
    let selectedDate = $("#selectedDate").val();
    
    let workHours = [];

    $(".work-hour-slot").each(function () {
      let start = $(this).find(".work-start-time").val();
      let end = $(this).find(".work-end-time").val();
      let maxAppointments = $(this).find(".work-max-appointments").val();

      workHours.push({
        start,
        end,
        max_appointments: maxAppointments
      });
    });

    $.ajax({
      url: "{{ route('doctor.update_work_schedule') }}",
      method: "POST",
      data: {
        date: $("#selectedDate").val(),
        work_hours: JSON.stringify(workHours),
        _token: $("meta[name='csrf-token']").attr("content")
      },
      success: function (response) {
        if (response.status) {
          Swal.fire("موفقیت", "ساعات کاری بروزرسانی شد.", "success");
        } else {
          Swal.fire("خطا", "بروزرسانی انجام نشد!", "error");
        }
      },
      error: function () {
        Swal.fire("خطا", "مشکلی در ذخیره تغییرات وجود دارد.", "error");
      }
    });

  }
 function attachDayClickEvents() {
  $('.calendar-day').not('.empty').off('click').on('click', function() {
   const selectedDayElement = $(this);
   const persianDate = selectedDayElement.data('date');
   const gregorianDate = moment(persianDate, 'jYYYY-jMM-jDD').format('YYYY-MM-DD');

   // پاک کردن محتوای قبلی مودال
   $('#dateModal').find('.modal-body').html('<div class="text-center py-3"><span>در حال بارگذاری...</span></div>');
   $('#dateModal').data('selectedDayElement', selectedDayElement);
   $('#dateModal').data('selectedDate', gregorianDate);

   $.ajax({
    url: "{{ route('doctor.get_holiday_status') }}",
    method: 'POST',
    data: {
     date: gregorianDate,
     _token: '{{ csrf_token() }}'
    },
    success: function(response) {
     updateModalContent(response); // به‌روزرسانی محتوای مودال
    },
    error: function() {
     Swal.fire('خطا', 'مشکلی در ارتباط با سرور وجود دارد.', 'error');
    }
   });

   $('#dateModal').modal('show');
  });
 }


 function populateSelectBoxes() {
  const yearSelect = $('#year');
  const monthSelect = $('#month');
  const currentYear = moment().jYear();
  const currentMonth = moment().jMonth() + 1;
  for (let year = currentYear - 10; year <= currentYear + 10; year++) {
   yearSelect.append(new Option(year, year));
  }
  const persianMonths = ["فروردین", "اردیبهشت", "خرداد", "تیر", "مرداد", "شهریور", "مهر", "آبان", "آذر", "دی",
   "بهمن", "اسفند"
  ];
  for (let month = 1; month <= 12; month++) {
   monthSelect.append(new Option(persianMonths[month - 1], month));
  }
  yearSelect.val(currentYear);
  monthSelect.val(currentMonth);
  yearSelect.change(function() {
   generateCalendar(yearSelect.val(), monthSelect.val());
  });
  monthSelect.change(function() {
   generateCalendar(yearSelect.val(), monthSelect.val());
  });
 }

 function populateRescheduleSelectBoxes() {
  const yearSelect = $('#year-reschedule');
  const monthSelect = $('#month-reschedule');
  const currentYear = moment().jYear();
  const currentMonth = moment().jMonth() + 1;
  yearSelect.empty();
  monthSelect.empty();
  // پر کردن سال‌ها
  for (let year = currentYear - 10; year <= currentYear + 10; year++) {
   yearSelect.append(new Option(year, year));
  }
  // پر کردن ماه‌ها
  const persianMonths = ["فروردین", "اردیبهشت", "خرداد", "تیر", "مرداد", "شهریور", "مهر", "آبان", "آذر", "دی",
   "بهمن", "اسفند"
  ];
  for (let month = 1; month <= 12; month++) {
   monthSelect.append(new Option(persianMonths[month - 1], month));
  }
  yearSelect.val(currentYear);
  monthSelect.val(currentMonth);
  // تغییرات سال و ماه
  yearSelect.off('change').on('change', function() {
   generateRescheduleCalendar(yearSelect.val(), monthSelect.val());
  });
  monthSelect.off('change').on('change', function() {
   generateRescheduleCalendar(yearSelect.val(), monthSelect.val());
  });
 }

 function generateRescheduleCalendar(year, month) {
  const rescheduleCalendarBody = $('#calendar-reschedule');
  rescheduleCalendarBody.empty();

  const today = moment().startOf('day').locale('fa');
  const firstDayOfMonth = moment(`${year}-${month}-01`, 'jYYYY-jMM-jDD').locale('fa').startOf('month');
  const daysInMonth = firstDayOfMonth.jDaysInMonth();
  const firstDayWeekday = firstDayOfMonth.weekday();

  // افزودن روزهای خالی
  for (let i = 0; i < firstDayWeekday; i++) {
   rescheduleCalendarBody.append('<div class="calendar-day empty"></div>');
  }

  // ایجاد روزهای ماه
  for (let day = 1; day <= daysInMonth; day++) {
   const currentDay = firstDayOfMonth.clone().add(day - 1, 'days');
   const isToday = currentDay.isSame(today, 'day');
   const dayClass = `calendar-day ${isToday ? 'active' : ''}`;
   rescheduleCalendarBody.append(`
            <div class="${dayClass} position-relative" data-date="${currentDay.format('jYYYY-jMM-jDD')}">
                <span>${currentDay.format('jD')}</span>
            </div>
        `);
  }

  attachRescheduleDayClickEvents();
 }

 function attachRescheduleDayClickEvents() {
  $('#calendar-reschedule .calendar-day').not('.empty').click(function() {
   const selectedDate = $(this).data('date');
   const gregorianDate = moment(selectedDate, 'jYYYY-jMM-jDD').format('YYYY-MM-DD');
   const today = moment().format('YYYY-MM-DD');
   const isHoliday = $(this).hasClass('holiday');
   $('#calendar-reschedule .calendar-day').removeClass('active');
   $(this).addClass('active');
   const hasAppointment = $(this).find('.my-badge-success').length > 0;

   if (gregorianDate < today) {
    Swal.fire('خطا', 'نمی‌توانید نوبت‌ها را به تاریخ‌های گذشته منتقل کنید.', 'error');
   } else if (isHoliday) {
    Swal.fire('خطا', 'این روز تعطیل است و امکان جابجایی نوبت به این روز وجود ندارد.', 'error');
   } else if (hasAppointment) {
    Swal.fire('خطا', 'این روز دارای نوبت فعال است و امکان جابجایی نوبت به این روز وجود ندارد.', 'error');
   } else {
    Swal.fire({
     title: 'تایید جابجایی نوبت',
     text: `آیا می‌خواهید نوبت‌ها به تاریخ ${moment(selectedDate, 'jYYYY-jMM-jDD').locale('fa').format('jD jMMMM jYYYY')} منتقل شوند؟`,
     icon: 'question',
     showCancelButton: true,
     confirmButtonText: 'بله، جابجا کن',
     cancelButtonText: 'لغو',
    }).then((result) => {
     if (result.isConfirmed) {
      const oldDate = $('#dateModal').data('selectedDate');
      $.ajax({
       url: "{{ route('doctor.reschedule_appointment') }}",
       method: 'POST',
       data: {
        old_date: oldDate,
        new_date: gregorianDate,
        _token: '{{ csrf_token() }}',
       },
       success: function(response) {
        if (response.status) {
         Swal.fire('موفقیت', response.message, 'success');
         loadAppointmentsCount(); // بروزرسانی نوبت‌ها
         loadHolidayStyles(); // بروزرسانی استایل تعطیلات
        } else {
         Swal.fire('خطا', response.message, 'error');
        }
       },
       error: function(xhr) {
        

        let errorMessage = 'مشکلی در ارتباط با سرور رخ داده است.';
        if (xhr.status === 400 && xhr.responseJSON && xhr.responseJSON.message) {
         errorMessage = xhr.responseJSON.message; // دریافت پیام خطای سرور
        }

        // نمایش پیام مناسب در سوئیت الرت
        Swal.fire('خطا', errorMessage, 'error');
       }
      });

     }
    });
   }
  });
 }


 const appointmentsCountUrl = "{{ route('appointments.count') }}";

 function loadAppointmentsCount() {
  $.ajax({
   url: "{{ route('appointments.count') }}",
   method: 'GET',
   success: function(response) {
    if (response.status) {
     $('.calendar-day').each(function() {
      const persianDate = $(this).data('date');
      const gregorianDate = moment(persianDate, 'jYYYY-jMM-jDD').format('YYYY-MM-DD');
      const appointment = response.data.find(a => a.appointment_date === gregorianDate);
      $(this).find('.my-badge-success').remove();
      if (appointment) {
       $(this).append(`<span class="my-badge-success">${appointment.appointment_count}</span>`);
      }
     });
    }
   }
  });
 }

 function generateRescheduleCalendar(year, month) {
  const rescheduleCalendarBody = $('#calendar-reschedule');
  rescheduleCalendarBody.empty();
  const today = moment().startOf('day').locale('fa');
  const firstDayOfMonth = moment(`${year}-${month}-01`, 'jYYYY-jMM-jDD').locale('fa').startOf('month');
  const daysInMonth = firstDayOfMonth.jDaysInMonth();
  const firstDayWeekday = firstDayOfMonth.weekday();
  // افزودن روزهای خالی اول ماه
  for (let i = 0; i < firstDayWeekday; i++) {
   rescheduleCalendarBody.append('<div class="calendar-day empty"></div>');
  }
  // ایجاد روزهای ماه
  for (let day = 1; day <= daysInMonth; day++) {
   const currentDay = firstDayOfMonth.clone().add(day - 1, 'days');
   const isToday = currentDay.isSame(today, 'day');
   const dayClass = `calendar-day ${isToday ? 'active' : ''}`;
   const dayElement = `
            <div class="${dayClass} position-relative" data-date="${currentDay.format('jYYYY-jMM-jDD')}">
                <span>${currentDay.format('jD')}</span>
            </div>`;
   rescheduleCalendarBody.append(dayElement);
  }
  loadAppointmentsCountInReschedule();
  loadHolidayStylesInReschedule();
  attachRescheduleDayClickEvents();
 }

 function loadAppointmentsCountInReschedule() {
  $.ajax({
   url: "{{ route('appointments.count') }}",
   method: 'GET',
   success: function(response) {
    if (response.status) {
     $('#calendar-reschedule .calendar-day').each(function() {
      const persianDate = $(this).data('date');
      const gregorianDate = moment(persianDate, 'jYYYY-jMM-jDD').format('YYYY-MM-DD');
      const appointment = response.data.find(a => a.appointment_date === gregorianDate);
      // حذف استایل قبلی
      $(this).find('.my-badge-success').remove();
      if (appointment) {
       $(this).append(`<span class="my-badge-success">${appointment.appointment_count}</span>`);
      }
     });
    }
   }
  });
 }

 function loadHolidayStylesInReschedule() {
  $.ajax({
   url: "{{ route('doctor.get_holidays') }}",
   method: 'GET',
   success: function(response) {
    if (response.status) {
     const holidays = response.holidays;
     $('#calendar-reschedule .calendar-day').each(function() {
      const persianDate = $(this).data('date');
      const gregorianDate = moment(persianDate, 'jYYYY-jMM-jDD').format('YYYY-MM-DD');
      if (holidays.includes(gregorianDate)) {
       $(this).addClass('holiday');
      } else {
       $(this).removeClass('holiday');
      }
     });
    }
   }
  });
 }

 function updateModalContent(response) {
  // ابتدا پاک کردن محتوای مودال
  const modalBody = $('#dateModal .modal-body');
  modalBody.empty();

  if (!response || !response.status) {
   modalBody.html('<div class="alert alert-danger">خطایی در دریافت اطلاعات رخ داده است.</div>');
   return;
  }

  // بررسی وضعیت تعطیلی
  if (response.is_holiday) {
   modalBody.html(`
            <div class="alert alert-info">
                این روز تعطیل است. آیا می‌خواهید آن را از حالت تعطیلی خارج کنید؟
            </div>
            <div class="d-flex justify-content-between mt-3 gap-4">
                <button id="confirmUnHolidayButton" class="btn btn-primary h-50 w-100 me-2">بله</button>
                <button class="btn btn-danger h-50 w-100 close-modal" data-bs-dismiss="modal" aria-label="Close">خیر</button>
            </div>
        `);
  }
  // بررسی وجود نوبت
  else if (response.data && response.data.length > 0) {
   modalBody.html(`
            <div class="alert alert-info">
                شما برای این روز نوبت فعال دارید.
            </div>
           <div id="workHoursContainer">
            </div>
            <button id="updateWorkHours" onclick="updateWorkhours()" class="btn btn-primary w-100 h-50 mt-3" style="display: none;">
              بروزرسانی ساعات کاری
             </button>
            <div class="d-flex justify-content-between mt-3 gap-4">
                <button class="btn btn-danger h-50 w-100 close-modal me-2 cancle-btn-appointment">لغو نوبت‌ها</button>
                <button class="btn btn-secondary w-100 btn-reschedule h-50">جابجایی نوبت‌ها</button>
            </div>
        `);
  }
  // روز بدون نوبت و بدون تعطیلی
  else {
   modalBody.html(`
            <div class="alert alert-info">
                شما برای این روز نوبت فعالی ندارید. آیا می‌خواهید این روز را تعطیل کنید؟
            </div>
               <div id="workHoursContainer">

            </div>
            <button id="updateWorkHours" onclick="updateWorkhours()" class="btn btn-primary w-100 h-50 mt-3" style="display: none;">
              بروزرسانی ساعات کاری
             </button>
            <div class="d-flex justify-content-between mt-3 gap-4">
                <button id="confirmHolidayButton" class="btn btn-primary h-50 w-100 me-2">بله</button>
               <button class="btn btn-danger h-50 w-100 close-modal" data-bs-dismiss="modal" aria-label="Close">خیر</button>
            </div>
        `);
  }
 }

 const toggleHolidayUrl = "{{ route('doctor.toggle_holiday') }}";
 const getHolidaysUrl = "{{ route('doctor.get_holidays') }}";
 // بارگذاری استایل روزهای تعطیل هنگام لود صفحه
 function loadHolidayStyles() {
  $.ajax({
   url: "{{ route('doctor.get_holidays') }}",
   method: 'GET',
   success: function(response) {
    if (response.status) {
     const holidays = response.holidays;
     $('.calendar-day').each(function() {
      const persianDate = $(this).data('date');
      const gregorianDate = moment(persianDate, 'jYYYY-jMM-jDD').format('YYYY-MM-DD');
      if (holidays.includes(gregorianDate)) {
       $(this).addClass('holiday');
      } else {
       $(this).removeClass('holiday');
      }
     });
    }
   }
  });
 }

 function findNextAvailableAppointment() {
  $.ajax({
   url: "{{ route('doctor.get_next_available_date') }}",
   method: 'GET',
   success: function(response) {
    if (response.status) {
     const nextAvailableDate = response.date;

     Swal.fire({
      title: 'اولین نوبت خالی',
      html: `آیا می‌خواهید به تاریخ ${moment(nextAvailableDate, 'YYYY-MM-DD').locale('fa').format('jD jMMMM jYYYY')} منتقل شوید؟`,
      icon: 'question',
      showCancelButton: true,
      confirmButtonText: 'بله',
      cancelButtonText: 'خیر'
     }).then((result) => {
      if (result.isConfirmed) {
       // آپدیت تاریخ اولین نوبت در دیتابیس
       $.ajax({
        url: "{{ route('doctor.update_first_available_appointment') }}",
        method: 'POST',
        data: {
         date: nextAvailableDate,
         _token: '{{ csrf_token() }}'
        },
        success: function(updateResponse) {
         if (updateResponse.status) {
          Swal.fire({
           title: 'موفقیت',
           text: `نوبت به تاریخ ${moment(nextAvailableDate, 'YYYY-MM-DD').locale('fa').format('jD jMMMM jYYYY')} منتقل شد.`,
           icon: 'success'
          });

          // بروزرسانی تقویم
          loadAppointmentsCount();
          loadHolidayStyles();
         } else {
          Swal.fire('خطا', updateResponse.message, 'error');
         }
        },
        error: function() {
         Swal.fire('خطا', 'مشکلی در ارتباط با سرور وجود دارد.', 'error');
        }
       });
      }
     });
    } else {
     Swal.fire('اطلاع', response.message, 'info');
    }
   },
   error: function() {
    Swal.fire('خطا', 'مشکلی در ارتباط با سرور وجود دارد.', 'error');
   }
  });
 }
 $('#goToFirstAvailable').on('click', function() {
  $.ajax({
   url: "{{ route('doctor.get_next_available_date') }}",
   method: 'GET',
   success: function(response) {
    if (response.status) {
     const nextAvailableDate = response.date; // تاریخ اولین نوبت خالی
     const oldDate = $('#dateModal').data('selectedDate'); // تاریخ قبلی از مودال اولیه

     Swal.fire({
      title: `اولین نوبت خالی (${moment(nextAvailableDate, 'YYYY-MM-DD').locale('fa').format('jD jMMMM jYYYY')})`,
      text: `آیا می‌خواهید نوبت از تاریخ ${moment(oldDate, 'YYYY-MM-DD').locale('fa').format('jD jMMMM jYYYY')} به تاریخ ${moment(nextAvailableDate, 'YYYY-MM-DD').locale('fa').format('jD jMMMM jYYYY')} منتقل شود؟`,
      icon: 'question',
      showCancelButton: true,
      confirmButtonText: 'بله، جابجا کن',
      cancelButtonText: 'لغو',
     }).then((result) => {
      if (result.isConfirmed) {
       // ارسال درخواست برای جابجایی
       $.ajax({
        url: "{{ route('doctor.update_first_available_appointment') }}",
        method: 'POST',
        data: {
         old_date: oldDate,
         new_date: nextAvailableDate,
         _token: '{{ csrf_token() }}',
        },
        success: function(updateResponse) {
         if (updateResponse.status) {
          Swal.fire('موفقیت', updateResponse.message, 'success');
          // بروزرسانی تقویم
          loadAppointmentsCount();
          loadHolidayStyles();
         } else {
          Swal.fire('خطا', updateResponse.message, 'error');
         }
        },
        error: function() {
         Swal.fire('خطا', 'مشکلی در ارتباط با سرور وجود دارد.', 'error');
        },
       });
      }
     });
    } else {
     Swal.fire('اطلاع', response.message, 'info');
    }
   },
   error: function() {
    Swal.fire('خطا', 'مشکلی در ارتباط با سرور وجود دارد.', 'error');
   },
  });
 });

 // اضافه کردن event listener به دکمه
 function getWorkHours(selectedDate) {
    $.ajax({
      url: "{{ route('doctor.get_default_schedule') }}",
      method: "GET",
      data: { date: selectedDate },
      success: function (response) {
        $("#workHoursContainer").empty();

        if (response.status && response.work_hours.length > 0) {
          response.work_hours.forEach((slot, index) => {
            $("#workHoursContainer").append(`
                        <h6 class="font-weight-bold">برنامه کاری</h6>
                        <div class="p-3 border mt-2">
                          <input type="hidden" id="selectedDate" value="${selectedDate}">

                            <div class="work-hour-slot d-flex justify-content-center gap-4">
                                <div class="position-relative">
                                    <label class="label-top-input-special-takhasos">شروع:</label>
                                    <input type="text" class="form-control h-50 work-start-time" value="${slot.start}" data-index="${index}" />
                                </div>
                                <div class="position-relative">
                                    <label class="label-top-input-special-takhasos">پایان:</label>
                                    <input type="text" class="form-control h-50 work-end-time" value="${slot.end}" data-index="${index}" />
                                </div>
                                <div class="position-relative">
                                    <label class="label-top-input-special-takhasos">حداکثر نوبت:</label>
                                    <input type="number" class="form-control h-50 work-max-appointments" value="${slot.max_appointments}" data-index="${index}" />
                                </div>
                            </div>
                        </div>
                    `);
          });

          $("#updateWorkHours").show();
        } else {
          $("#workHoursContainer").append(`<p class="text-center text-danger font-weight-bold">هیچ ساعات کاری برای این روز تعریف نشده است.</p>`);
          $("#updateWorkHours").hide();
        }
      },
      error: function () {
        Swal.fire("خطا", "مشکلی در دریافت ساعات کاری وجود دارد.", "error");
      }
    });
  }


 $(document).ready(function() {

  loadAppointmentsCount();
  $('#prev-month').click(function() {
   const yearSelect = $('#year');
   const monthSelect = $('#month');
   const currentMonth = parseInt(monthSelect.val());
   if (currentMonth === 1) {
    yearSelect.val(parseInt(yearSelect.val()) - 1).change();
    monthSelect.val(12).change();
   } else {
    monthSelect.val(currentMonth - 1).change();
   }
  });
  $('#next-month').click(function() {
   const yearSelect = $('#year');
   const monthSelect = $('#month');
   const currentMonth = parseInt(monthSelect.val());
   if (currentMonth === 12) {
    yearSelect.val(parseInt(yearSelect.val()) + 1).change();
    monthSelect.val(1).change();
   } else {
    monthSelect.val(currentMonth + 1).change();
   }
  });
  populateSelectBoxes();
  generateCalendar(moment().jYear(), moment().jMonth() + 1);
  $(document).on('click', '.cancle-btn-appointment', function() {
   const selectedDate = $('#dateModal').data('selectedDate');
   Swal.fire({
    title: 'آیا مطمئن هستید؟',
    text: "تمام نوبت‌های این روز لغو خواهند شد.",
    icon: 'warning',
    showCancelButton: true,
    confirmButtonText: 'بله، لغو کن!',
    cancelButtonText: 'لغو'
   }).then(result => {
    if (result.isConfirmed) {
     $.ajax({
      url: "{{ route('doctor.cancel_appointments') }}",
      method: 'POST',
      data: {
       date: selectedDate,
       _token: '{{ csrf_token() }}'
      },
      success: function(response) {
       if (response.status) {
        Swal.fire('موفقیت', response.message, 'success');
        $('#dateModal').modal('hide'); // بستن مودال
        loadAppointmentsCount(); // بروزرسانی تقویم
       } else {
        Swal.fire('خطا', response.message, 'error');
       }
      },
      error: function() {
       Swal.fire('خطا', 'مشکلی در ارتباط با سرور وجود دارد.', 'error');
      }
     });
    }
   });
  });

  // Modal for Appointment Reschedule
  $(document).on('click', '#confirmReschedule', function() {
   const oldDate = $('#dateModal').data('selectedDate');
   const newDate = $('#calendar-reschedule .calendar-day.active').data('date');

   if (!newDate) {
    Swal.fire('خطا', 'لطفاً یک روز جدید انتخاب کنید.', 'error');
    return;
   }

   $.ajax({
    url: "{{ route('doctor.reschedule_appointment') }}",
    method: 'POST',
    data: {
     old_date: oldDate,
     new_date: moment(newDate, 'jYYYY-jMM-jDD').format('YYYY-MM-DD'),
     _token: '{{ csrf_token() }}'
    },
    success: function(response) {
     if (response.status) {
      Swal.fire('موفقیت', response.message, 'success');
      $('#rescheduleModal').modal('hide');
      loadAppointmentsCount(); // بروزرسانی نوبت‌ها
      loadHolidayStyles(); // بروزرسانی استایل تعطیلات
     } else {
      Swal.fire('خطا', response.message, 'error');
     }
    },
    error: function() {
     Swal.fire('خطا', 'مشکلی در ارتباط با سرور وجود دارد.', 'error');
    }
   });
  });

  $(document).on('click', '.btn-reschedule', function() {
   const selectedDate = $('#dateModal').data('selectedDate');
   $('#rescheduleModal').modal('show'); // باز کردن مودال جابجایی نوبت‌ها

   // تولید تقویم برای جابجایی
   const year = moment(selectedDate, 'YYYY-MM-DD').jYear();
   const month = moment(selectedDate, 'YYYY-MM-DD').jMonth() + 1;

   generateRescheduleCalendar(year, month);
   populateRescheduleSelectBoxes();
  });

  $('.btn-reschedule').on('click', function() {


   $('#rescheduleModal').modal('show');
   const selectedDate = $('#dateModal').data('selectedDate');
   const year = moment(selectedDate, 'YYYY-MM-DD').jYear();
   const month = moment(selectedDate, 'YYYY-MM-DD').jMonth() + 1;
   generateRescheduleCalendar(year, month);
   populateRescheduleSelectBoxes();
   // اضافه کردن رویداد کلیک به روزهای تقویم جابجایی
   attachRescheduleDayClickEvents();
   // تولید تقویم جابجایی با همان داده‌های اصلی
   generateCalendar(year, month);
   // اضافه کردن رویداد کلیک برای روزهای تقویم جابجایی
   $('#calendar-reschedule .calendar-day').not('.empty').click(function() {
    const targetDate = $(this).data('date');
    const isHoliday = $(this).hasClass('holiday');
    const hasAppointment = $(this).find('.my-badge-success').length > 0;
    if (isHoliday) {
     Swal.fire('اخطار', 'نمی‌توانید نوبت‌ها را به یک روز تعطیل منتقل کنید.', 'error');
    } else if (hasAppointment) {
     Swal.fire('اخطار', 'برای این روز نوبت فعال دارید. نمی‌توانید نوبت‌ها را جابجا کنید.', 'error');
    } else {
     Swal.fire({
      title: 'تأیید جابجایی',
      text: `آیا می‌خواهید نوبت‌ها را به تاریخ ${moment(targetDate, 'jYYYY-jMM-jDD').locale('fa').format('jD jMMMM jYYYY')} منتقل کنید؟`,
      icon: 'question',
      showCancelButton: true,
      confirmButtonText: 'بله',
      cancelButtonText: 'خیر',
     }).then(result => {
      if (result.isConfirmed) {
       // ارسال درخواست برای جابجایی نوبت
       const oldDate = $('#dateModal').data('selectedDate');
       $.ajax({
        url: "{{ route('doctor.reschedule_appointment') }}",
        method: 'POST',
        data: {
         old_date: selectedDate,
         new_date: moment(targetDate, 'jYYYY-jMM-jDD').format('YYYY-MM-DD'), // تبدیل به فرمت میلادی
         _token: '{{ csrf_token() }}'
        },
        success: function(response) {
         if (response.status) {
          Swal.fire('موفقیت', 'نوبت‌ها با موفقیت جابجا شدند.', 'success');
          $('#rescheduleModal').modal('hide');
          // به‌روزرسانی تقویم اصلی
          generateCalendar(moment().jYear(), moment().jMonth() + 1);
          loadAppointmentsCount(); // بروزرسانی نوبت‌ها
          loadHolidayStyles(); // بروزرسانی استایل تعطیلات
         } else {
          Swal.fire('خطا', response.message, 'error');
         }
        },
        error: function(xhr) {
         // پیام خطای سفارشی
         let errorMessage = 'مشکلی در ارتباط با سرور رخ داده است.';

         if (xhr.status === 400) {
          // متن ثابت برای خطای 400
          errorMessage = 'امکان جابجایی نوبت‌ها به گذشته وجود ندارد.';
         }

         // نمایش پیام خطا در سوئیت الرت
         Swal.fire('خطا', errorMessage, 'error');
        }
       });
      }
     });
    }
   });
  });
  $('#prev-month-reschedule, #next-month-reschedule').off('click').on('click', function() {
   const yearSelect = $('#year-reschedule');
   const monthSelect = $('#month-reschedule');
   const currentMonth = parseInt(monthSelect.val());

   if (this.id === 'prev-month-reschedule' && currentMonth === 1) {
    yearSelect.val(parseInt(yearSelect.val()) - 1).change();
    monthSelect.val(12).change();
   } else if (this.id === 'next-month-reschedule' && currentMonth === 12) {
    yearSelect.val(parseInt(yearSelect.val()) + 1).change();
    monthSelect.val(1).change();
   } else {
    monthSelect.val(this.id === 'prev-month-reschedule' ? currentMonth - 1 : currentMonth + 1).change();
   }

   // همگام‌سازی سلکت باکس‌ها با تقویم
   const newMonth = parseInt(monthSelect.val());
   const newYear = parseInt(yearSelect.val());
   generateRescheduleCalendar(newYear, newMonth);

   // تنظیم مقدار انتخاب‌شده در سلکت باکس
   monthSelect.val(newMonth);
   yearSelect.val(newYear);
  });

  $('.calendar-day').not('.empty').on('click', function() {
   const selectedDayElement = $(this);
   const persianDate = selectedDayElement.data('date');
   const gregorianDate = moment(persianDate, 'jYYYY-jMM-jDD').format('YYYY-MM-DD');
   $('#dateModal').data('selectedDayElement', selectedDayElement);
   $('#dateModal').data('selectedDate', gregorianDate);
   $('#dateModalLabel').text(
    `نوبت‌های ${moment(persianDate, 'jYYYY-jMM-jDD').locale('fa').format('jD jMMMM jYYYY')}`
   );
   // پاک کردن محتوای قبلی
   $('.not-appointment').addClass('d-none');
   $('.having-nobat-for-this-day').addClass('d-none');
   $.ajax({
    url: "{{ route('doctor.get_holiday_status') }}",
    method: 'POST',
    data: {
     date: gregorianDate,
     _token: '{{ csrf_token() }}'
    },
    success: function(response) {
     // حالت اول: روز تعطیل
     if (response.is_holiday) {
      $('.not-appointment').removeClass('d-none');
      $('.not-appointment .alert').html(`
            این روز قبلاً تعطیل شده است. 
            <div class="w-100 d-flex justify-content-between gap-4 mt-3">
              <div class="w-100">
                <button type="button" id="confirmUnHolidayButton" class="btn btn-primary h-50 w-100">بله</button>
              </div>
              <div class="w-100">
                <button type="button" class="btn btn-danger h-50 w-100 close-modal" data-dismiss="modal" aria-label="Close">خیر</button>
              </div>
            </div>
          `);
     }
     // حالت دوم: روز با نوبت فعال
     else if (response.data.length > 0) {
      $('.having-nobat-for-this-day').removeClass('d-none');
      // نمایش اطلاعات نوبت‌ها
  
      $('.having-nobat-for-this-day .alert').html(`
            پزشک گرامی شما برای این روز نوبت فعال دارید.
            <div class="w-100 d-flex justify-content-between gap-4 mt-3">
              <div class="w-100">
                <button class="btn btn-danger cancle-btn-appointment h-50 w-100">لغو نوبت ها</button>
              </div>
              <div class="w-100">
                <button class="btn btn-secondary btn-reschedule h-50 w-100">جابجایی نوبت ها</button>
              </div>
            </div>
          `);
     }
     // حالت سوم: روز بدون نوبت
     else {
      $('.not-appointment').removeClass('d-none');
      $('.not-appointment .alert').html(`
            پزشک گرامی شما برای این روز نوبت فعالی ندارید. 
            آیا می‌خواهید این روز را تعطیل کنید؟
            <div class="w-100 d-flex justify-content-between gap-4 mt-3">
              <div class="w-100">
                <button type="button" id="confirmHolidayButton" class="btn btn-primary h-50 w-100">بله</button>
              </div>
              <div class="w-100">
                <button type="button" class="btn btn-danger h-50 w-100 close-modal" data-dismiss="modal" aria-label="Close">خیر</button>
              </div>
            </div>
          `);
     }
     $(document).on('click', '.close-modal', function() {
      $('#dateModal').modal('hide');
     });

     // اضافه کردن event listener برای دکمه‌ها
     $(document).off('click', '#confirmHolidayButton, #confirmUnHolidayButton');
     $(document).on('click', '#confirmHolidayButton, #confirmUnHolidayButton', function() {
      const selectedDate = $('#dateModal').data('selectedDate');
      const selectedDayElement = $('#dateModal').data('selectedDayElement');

      $.ajax({
       url: "{{ route('doctor.toggle_holiday') }}",
       method: 'POST',
       data: {
        date: selectedDate,
        _token: '{{ csrf_token() }}'
       },
       success: function(response) {
        if (response.status) {
         if (response.is_holiday) {
          selectedDayElement.addClass('holiday');
         } else {
          selectedDayElement.removeClass('holiday');
         }
         $('#dateModal').modal('hide');
         Swal.fire({
          icon: 'success',
          title: response.message,
          confirmButtonText: 'باشه'
         });
        } else {
         Swal.fire('خطا', response.message, 'error');
        }
       },
       error: function() {
        Swal.fire('خطا', 'مشکلی در ارتباط با سرور رخ داده است.', 'error');
       }
      });
     });

    },
    error: function() {
     Swal.fire('خطا', 'مشکلی در ارتباط با سرور وجود دارد.', 'error');
    }
   });
   $('#dateModal').modal('show');
  });

  // تابع برای بروزرسانی محتوای مودال
  // فراخوانی هنگام بارگذاری صفحه
  loadHolidayStyles();
 });
 $(document).ready(function() {
  $(".calendar-day").on("click", function() {
    let persianDate = $(this).data("date"); // دریافت تاریخ شمسی
    let gregorianDate = moment(persianDate, 'jYYYY-jMM-jDD').format('YYYY-MM-DD'); // تبدیل به میلادی
    $("#selectedDate").val(gregorianDate); // ذخیره تاریخ میلادی در فیلد مخفی
   $("#selectedDate").val(gregorianDate); // ذخیره تاریخ میلادی در فیلد مخفی

   // بررسی تعطیل بودن روز
   $.ajax({
    url: "{{ route('doctor.get_holiday_status') }}",
    method: "POST",
    data: {
     date: gregorianDate,
     _token: '{{ csrf_token() }}'
    },
    success: function(response) {
     if (response.is_holiday) {
      // اگر روز تعطیل بود، فقط پیام تعطیلی را نمایش بدهد
      $(".not-appointment").removeClass("d-none");
      $(".having-nobat-for-this-day").addClass("d-none");
      $("#workHoursContainer").empty(); // حذف ساعات کاری
      $("#updateWorkHours").hide();
     } else {
      // اگر روز تعطیل نبود، ساعات کاری را دریافت کند
      getWorkHours(gregorianDate);
     }

     $("#dateModal").modal("show"); // باز کردن مودال
    }
   });
  });

 
  // ذخیره تغییرات ساعات کاری

    

 });
</script>
@endsection
