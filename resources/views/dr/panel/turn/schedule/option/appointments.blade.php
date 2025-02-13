<script>
 // با jQuery
 $(document).ready(function() {
  $('.btn-filter-appointment-toggle').on('click', function() {
   $(this).toggleClass('active');
   $('.appointments-filter-drop-toggle').toggleClass('show');
  });
  // بستن دراپ با کلیک خارج از المنت
  $(document).on('click', function(event) {
   if (!$(event.target).closest('.dropdown-container').length) {
    $('.btn-filter-appointment-toggle').removeClass('active');
    $('.appointments-filter-drop-toggle').removeClass('show');
   }
  });
  // انتخاب آیتم‌های فیلتر (به جز آیتم آخر)
  $('.appointments-filter-drop-toggle li:not(:last-child)').on('click', function() {
   // حذف کلاس bg-light-blue از همه آیتم‌ها
   $('.appointments-filter-drop-toggle li:not(:last-child)').removeClass('bg-light-blue');
   // اضافه کردن کلاس bg-light-blue به آیتم کلیک شده
   $(this).addClass('bg-light-blue');
  });
 });
 $(document).ready(function() {
  $('#userInfoModalCenter').on('show.bs.modal', function(event) {
   var button = $(event.relatedTarget); // دکمه‌ای که مودال را باز کرده است
   var time = button.data('time'); // زمان
   var date = button.data('date'); // تاریخ
   var fullname = button.data('fullname'); // نام و نام خانوادگی
   var mobile = button.data('mobile'); // موبایل
   var tracking_code = button.data('tracking-code'); // موبایل
   var nationalCode = button.data('national-code'); // کد ملی
   var paymentStatus = button.data('payment-status'); // وضعیت پرداخت
   var appointmentType = button.data('appointment-type'); // نوع نوبت
   var centerName = button.data('center-name'); // نام مرکز
   var modal = $(this);
   modal.find('.time-card .text-black').text(time); // زمان
   modal.find('.date-card .text-black').text(date); // تاریخ
   modal.find('.fullname').text(fullname); // نام و نام خانوادگی
   modal.find('.mobile').text(mobile); // موبایل
   modal.find('.national-code').text(nationalCode); // کد ملی
   modal.find('.payment-status').text(paymentStatus); // وضعیت پرداخت
   modal.find('.appointment-type').text(appointmentType); // نوع نوبت
   modal.find('.center-name').text(centerName); // نام مرکز
   modal.find('.tracking-code').text(tracking_code); // نام مرکز
  });
 });
  function getPaymentStatus(status) {
        switch (status) {
            case 'pending':
                return 'درحال پرداخت';
            case 'paid':
                return 'پرداخت شده';
            case 'unpaid':
                return 'پرداخت نشده';
            default:
                return '';
        }
    }

    function getAppointmentType(type) {
        switch (type) {
            case 'online':
                return 'آنلاین';
            case 'in_person':
                return 'حضوری';
            case 'phone':
                return 'تلفنی';
            default:
                return '';
        }
    }
 document.addEventListener('DOMContentLoaded', function() {
  const datepickerSpan = document.getElementById('datepicker');
  const appointmentsContainer = document.querySelector('.my-appointments-lists-cards');
  // تنظیم تاریخ اولیه
  const initialDate = moment().locale('fa').format('jYYYY/jMM/jDD');
  datepickerSpan.textContent = initialDate;

  function handleDateSelection(selectedDate) {
   // بروزرسانی تاریخ در اسپن
   datepickerSpan.textContent = selectedDate;
   // ارسال درخواست ایجکس برای بارگذاری نوبت‌ها
   $.ajax({
    url: "{{ route('dr.turn.my-appointments.by-date') }}",
    method: 'GET',
    data: {
     date: selectedDate
    },
    success: function(response) {
     // پاک کردن محتوای قبلی
     appointmentsContainer.innerHTML = '';
     // بررسی وجود نوبت‌ها
     if (response.appointments.length > 0) {
      // ایجاد HTML برای هر نوبت
      response.appointments.forEach(function(appointment) {
       // تبدیل تاریخ با احتیاط
       const appointment_date = moment(appointment.appointment_date);
       const formattedTime = appointment_date.format('HH:mm');
       const formattedDate = appointment_date.locale('fa').format('dddd، jD jMMMM');
       const appointmentHTML = `
                            <div class="my-appointments-lists-card w-100 d-flex justify-content-between align-items-center p-3 my-border">
                                <div class="d-flex align-items-center gap-10 cursor-pointer" 
                                    data-toggle="modal" 
                                    data-target="#userInfoModalCenter"
                                    data-time="${formattedTime}"
                                    data-tracking-code="${appointment.tracking_code}"
                                    data-date="${formattedDate}"
                                    data-fullname="${appointment.patient.first_name + ' ' + appointment.patient.last_name}"
                                    data-mobile="${appointment.patient.mobile}"
                                    data-national-code="${appointment.patient.national_code}"
                                    data-payment-status="${getPaymentStatus(appointment.payment_status)}"
                                    data-appointment-type="${getAppointmentType(appointment.appointment_type)}"
                                    data-center-name="${appointment.clinic ? appointment.clinic.name : ''}">
                                    <button class="btn h-50 border border-success bg-light-success d-flex justify-content-center align-items-center">
                                        ${formattedTime}
                                    </button>
                                    <div class="d-flex flex-column gap-10">
                                        <span class="font-weight-bold">
                                            ${appointment.patient.first_name + ' ' + appointment.patient.last_name}
                                        </span>
                                        <span class="font-weight-light font-size-13">
                                            ${appointment.patient.mobile}
                                        </span>
                                        <span class="font-weight-light text-danger font-size-13">
                                            ${getPaymentStatus(appointment.payment_status)}
                                        </span>
                                    </div>
                                </div>
                                <div>
                                    <span class="font-size-13 font-weight-bold">
                                        ${appointment.patient.national_code}
                                    </span>
                                </div>
                                <div>
                                    <button class="btn btn-outline-info" data-toggle="modal" data-target="#endVisitModalCenter">
                                        پایان ویزیت
                                    </button>
                                </div>
                            </div>
                        `;
       appointmentsContainer.insertAdjacentHTML('beforeend', appointmentHTML);
      });
     } else {
      // نمایش پیغام عدم وجود نوبت
      appointmentsContainer.innerHTML = `
                        <div class="container-fluid h-50 d-flex justify-content-center align-items-center align-self-center">
                            <div class="text-center">
                                <p class="font-weight-bold">
                                    برای تاریخی که انتخاب کردید، در مرکز موردنظر هیچ نوبتی موجود نیست.
                                </p>
                            </div>
                        </div>
                    `;
     }
    },
    error: function() {
     alert('خطا در بارگذاری نوبت‌ها');
    }
   });
  }
  // توابع کمکی برای تبدیل وضعیت‌ها
 
  // اضافه کردن رویداد به روزهای تقویم
  const calendarBody = document.getElementById('calendar-body');
  // رویداد کلیک سراسری روی calendarBody
  calendarBody.addEventListener('click', function(e) {
   const target = e.target.closest('.calendar-day'); // پیدا کردن والد calendar-day
   if (target && !target.classList.contains('empty')) {
    const selectedDate = target.getAttribute('data-date');
    handleDateSelection(selectedDate);
    $('#calendarModal').modal('hide');
   }
  });
 });

 $(document).ready(function() {
  let selectedDate = $('#datepicker').text().trim();

  $('.appointments-filter-drop-toggle li').on('click', function() {
   let filterType = $(this).text().trim();

   if ($(this).find('span').text().includes('فعالسازی نوبت دهی مطب')) {
    window.location.href = 'آدرس_مشخص_شده_توسط_کاربر';
    return;
   }

   let appointmentsContainer = $('#appointment-lists-container');
   let loadingSpinner = $(
    '<div class="text-center w-100"><div class="spinner-border text-primary" role="status"><span class="sr-only">در حال بارگذاری...</span></div></div>'
    );

   appointmentsContainer.empty().append(loadingSpinner); // نمایش لودینگ

   $('.btn-filter-appointment-toggle span.text-btn-425').text(filterType);

   $.ajax({
    url: "{{ route('dr.turn.filter-appointments') }}",
    type: "GET",
   data: {
           type: filterType === 'کل نوبت ها' ? '' : (filterType === 'نوبت های مطب' ? 'in_person' : 'online'),
           date: selectedDate
       },

    success: function(response) {
        

     appointmentsContainer.empty(); // پاک کردن لودینگ بعد از دریافت داده‌ها
        
      if (response.appointments.length > 0) {
            // ایجاد HTML برای هر نوبت
          response.appointments.forEach(function (appointment) {
              // تبدیل تاریخ با احتیاط
              const appointment_date = moment(appointment.appointment_date);
              const formattedTime = appointment_date.format('HH:mm');
              const formattedDate = appointment_date.locale('fa').format('dddd، jD jMMMM');
        let appointmentHTML = `
                  <div class="my-appointments-lists-card w-100 d-flex justify-content-between align-items-center p-3 my-border">
                                <div class="d-flex align-items-center gap-10 cursor-pointer"
                                    data-toggle="modal"
                                    data-target="#userInfoModalCenter"
                                    data-time="${formattedTime}"
                                    data-tracking-code="${appointment.tracking_code}"
                                    data-date="${formattedDate}"
                                    data-fullname="${appointment.patient.first_name + ' ' + appointment.patient.last_name}"
                                    data-mobile="${appointment.patient.mobile}"
                                    data-national-code="${appointment.patient.national_code}"
                                    data-payment-status="${getPaymentStatus(appointment.payment_status)}"
                                    data-appointment-type="${getAppointmentType(appointment.appointment_type)}"
                                    data-center-name="${appointment.clinic ? appointment.clinic.name : ''}">
                                    <button class="btn h-50 border border-success bg-light-success d-flex justify-content-center align-items-center">
                                        ${formattedTime}
                                    </button>
                                    <div class="d-flex flex-column gap-10">
                                        <span class="font-weight-bold">
                                            ${appointment.patient.first_name + ' ' + appointment.patient.last_name}
                                        </span>
                                        <span class="font-weight-light font-size-13">
                                            ${appointment.patient.mobile}
                                        </span>
                                        <span class="font-weight-light text-danger font-size-13">
                                            ${getPaymentStatus(appointment.payment_status)}
                                        </span>
                                    </div>
                                </div>
                                <div>
                                    <span class="font-size-13 font-weight-bold">
                                        ${appointment.patient.national_code}
                                    </span>
                                </div>
                                <div>
                                    <button class="btn btn-outline-info" data-toggle="modal" data-target="#endVisitModalCenter">
                                        پایان ویزیت
                                    </button>
                                </div>
                            </div>`;

        appointmentsContainer.append(appointmentHTML);

       });
      } else {
       appointmentsContainer.html('<div class="text-center w-100">نوبتی یافت نشد.</div>');
     }
    },
    complete: function() {
     $('.appointments-filter-drop-toggle').removeClass('show'); // بستن منوی فیلتر
    },
    error: function() {
     appointmentsContainer.html('<div class="text-center w-100 text-danger">خطا در بارگذاری نوبت‌ها.</div>');
    }
   });
  });

  $('#calendar-body').on('click', '.calendar-day', function() {
   if (!$(this).hasClass('empty')) {
    selectedDate = $(this).data('date');
    $('#datepicker').text(selectedDate);
   }
  });
 });
</script>
