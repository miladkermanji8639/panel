{{-- resources\views\dr\panel\my-tools\workhours.blade.php --}}
<script>
 $(document).ready(function() {
  // وقتی مودال بسته می‌شود
  $(document).on('hidden.bs.modal', '.modal', function() {
   // حذف تمام بک‌دراپ‌های باقی‌مانده
   $('.modal-backdrop').remove();
   // اطمینان از حذف کلاس modal-open از body
   $('body').removeClass('modal-open');
   // اطمینان از حذف خاصیت استایل اضافه‌شده
   $('body').css('padding-right', '');
  });
 });
 $(document).on('change', '#select-all-copy-modal', function() {
  const isChecked = $(this).is(':checked');
  $('#checkboxModal input[type="checkbox"]').not(this).prop('checked', isChecked);
 });
 function validateTimeSlot(startTime, endTime) {
  // تبدیل زمان‌ها به دقیقه
  const startMinutes = timeToMinutes(startTime);
  const endMinutes = timeToMinutes(endTime);
  // بررسی اینکه زمان پایان از زمان شروع بزرگتر باشد
  if (startMinutes >= endMinutes) {
   toastr.error('زمان پایان باید بزرگتر از زمان شروع باشد')
   return false;
  }
  // بررسی تداخل با برنامه کاری‌های موجود
  const existingSlots = $(`#morning-${day}-details .form-row`);
  let hasConflict = false;
  const conflictingSlots = [];
  existingSlots.each(function() {
   const existingStart = $(this).find('.start-time').val();
   const existingEnd = $(this).find('.end-time').val();
   if (isTimeConflict(startTime, endTime, existingStart, existingEnd)) {
    conflictingSlots.push({
     start: existingStart,
     end: existingEnd
    });
    hasConflict = true;
    return false; // خروج از حلقه
   }
  });
  if (hasConflict) {
   toastr.error('این بازه زمانی با برنامه کاری‌های موجود تداخل دارد');
   return false;
  }
  return true;
 }
 function timeToMinutes(time) {
  const [hours, minutes] = time.split(':').map(Number);
  return hours * 60 + minutes;
 }
 function isTimeConflict(newStart, newEnd, existingStart, existingEnd) {
  const newStartMinutes = timeToMinutes(newStart);
  const newEndMinutes = timeToMinutes(newEnd);
  const existingStartMinutes = timeToMinutes(existingStart);
  const existingEndMinutes = timeToMinutes(existingEnd);
  return (
   (newStartMinutes < existingEndMinutes && newEndMinutes > existingStartMinutes)
  );
 }
 function initializeTimepicker() {
  const DOMElement = $(".timepicker-ui");
  const options = {
   clockType: '24h',
   theme: 'basic',
   mobile: true,
   enableScrollbar: true,
   disableTimeRangeValidation: false,
   autoClose: true
  };
  DOMElement.each(function() {
   if (!$(this).data('timepicker-initialized')) { // بررسی اینکه آیا قبلاً راه‌اندازی شده است
    const newTimepicker = new window.tui.TimepickerUI(this, options);
    newTimepicker.create();
    $(this).data('timepicker-initialized', true); // علامت‌گذاری به عنوان راه‌اندازی شده
   }
  });
 }
 $(document).ready(function() {
  setTimeout(() => {
   initializeTimepicker();
  }, 3000);
 });
 $(document).on('dynamicContentLoaded', function() {
  initializeTimepicker(); // Initialize timepicker for dynamically loaded content
 });
 // تابع برای بررسی و فعال/غیرفعال کردن دکمه کپی
 // بررسی وضعیت دکمه کپی برای همه روزها
 // در زمان بارگذاری صفحه
 $(document).on('click', '#saveSelection', function() {
  const sourceDay = 'saturday'; // مقدار روز مبدأ
  const targetDays = [];
  // جمع‌آوری روزهای انتخاب‌شده
  $('#checkboxModal input[type="checkbox"]:checked').each(function() {
   if ($(this).attr('id') !== 'select-all-copy-modal') {
    targetDays.push($(this).attr('id').replace('-copy-modal', ''));
   }
  });
  if (targetDays.length === 0) {
   toastr.error('لطفاً حداقل یک روز را انتخاب کنید');
   return;
  }
  showLoading();
  $.ajax({
   url: "{{ route('copy-work-hours') }}",
   method: 'POST',
   data: {
    source_day: sourceDay,
    target_days: targetDays,
    override: 0 ?? false,
    _token: '{{ csrf_token() }}'
   },
   success: function(response) {
    hideLoading();
    toastr.success('ساعات کاری با موفقیت کپی شد');
    $("#checkboxModal").modal("hide"); // بستن مدال
    $("#checkboxModal").removeClass("show");
    $(".modal-backdrop").remove();
    loadWorkSchedule(response)
    response.workSchedules.forEach(function(schedule) {
     const day = schedule.day; // روز مقصد
     // 1. فعال کردن تیک روز مقصد
     $(`#${day}`).prop('checked', true);
     // 2. نمایش بخش ساعات کاری برای روز مقصد
     $(`.work-hours-${day}`).removeClass('d-none');
     // 3. به‌روزرسانی محتوا (برنامه کاری‌های جدید) برای روز مقصد
     reloadDayData(day);
     loadWorkSchedule(response)
    });
   },
   error: function(xhr) {
    hideLoading();
    // بررسی خطای تداخل
    if (xhr.status === 400) {
     const conflict = Array.isArray(xhr.responseJSON.conflicting_slots) ?
      xhr.responseJSON.conflicting_slots : []; // اطمینان از اینکه یک آرایه است
     let conflictMessage = 'بازه‌های زمانی  تداخل دارند: آیا میخواهید جایگزین شود؟؟<br><ul>';
     conflict.forEach(slot => {
      conflictMessage += `<li>${slot.start} تا ${slot.end}</li>`;
     });
     conflictMessage += '</ul>';
     Swal.fire({
      title: 'تداخل بازه‌های زمانی',
      html: conflictMessage,
      icon: 'warning',
      showCancelButton: true,
      confirmButtonText: 'بله، جایگزین شود',
      cancelButtonText: 'لغو',
      reverseButtons: true
     }).then((result) => {
      if (result.isConfirmed) {
       // ارسال درخواست برای جایگزینی
       $.ajax({
        url: "{{ route('copy-work-hours') }}",
        method: 'POST',
        data: {
         source_day: sourceDay,
         target_days: targetDays,
         override: true,
         _token: '{{ csrf_token() }}'
        },
        success: function(response) {
         response.target_days.forEach(function(day) {
          const dayCheckbox = $(`#${day}`);
          dayCheckbox.prop('checked', true);
          // 2. نمایش بخش ساعات کاری مربوط به روز مقصد
          $(`.work-hours-${day}`).removeClass('d-none');
          reloadDayData(day);
          loadWorkSchedule(response)
         });
         // بازسازی داده‌های روز مقصد
         toastr.success(response.message);
         $("#checkboxModal").modal("hide"); // بستن مدال
         $("#checkboxModal").removeClass("show");
         $(".modal-backdrop").remove();
         loadWorkSchedule(response)
         // به‌روزرسانی رابط کاربری برای روزهای مقصد
         response.workSchedules.forEach(function(schedule) {
          updateDayUI(schedule);
          loadWorkSchedule(response)
         });
        },
        error: function(xhr) {
         toastr.error(xhr.responseJSON?.message || 'خطا در کپی ساعات کاری');
        }
       });
      } else {
       toastr.warning('عملیات جایگزینی لغو شد')
      }
     });
    } else {
     toastr.error(xhr.responseJSON?.message || 'خطا در کپی ساعات کاری')
    }
   }
  });
 });
 function reloadDayData(day) {
  $.ajax({
   url: "{{ route('dr-get-work-schedule') }}",
   method: 'GET',
   success: function(response) {
    const schedule = response.workSchedules.find(schedule => schedule.day === day);
    if (schedule) {
     updateDayUI(schedule);
    }
   },
   error: function(xhr) {
    console.error("خطا در دریافت ساعات کاری:", xhr.responseText);
   }
  });
 }
 // تابع برای به‌روزرسانی رابط کاربری روز مقصد
 function updateDayUI(schedule) {
  const day = schedule.day; // روز مقصد
  const $container = $(`#morning-${day}-details`);
  // پاک کردن محتوای قبلی
  $container.empty();
  // اضافه کردن ردیف جدید
  const mainRowHtml = `
        <div class="form-row w-100 d-flex justify-content-between align-items-center border-bottom-2">
            <div class="d-flex justify-content-start align-items-center gap-4 mt-2 mb-3">
                <div class="form-group position-relative timepicker-ui">
                    <label for="morning-start-${day}" class="label-top-input-special-takhasos">از</label>
                    <input type="text" class="form-control h-50 timepicker-ui-input text-center font-weight-bold font-size-13" id="morning-start-${day}" value="${schedule.start_time || ''}">
                </div>
                <div class="form-group position-relative timepicker-ui">
                    <label for="morning-end-${day}" class="label-top-input-special-takhasos">تا</label>
                    <input type="text" class="form-control h-50 timepicker-ui-input text-center font-weight-bold font-size-13" id="morning-end-${day}" value="${schedule.end_time || ''}">
                </div>
                <div class="form-group col-sm-3 position-relative">
                    <label for="morning-patients-${day}" class="label-top-input-special-takhasos">تعداد نوبت</label>
                    <input type="text" readonly class="form-control h-50 text-center" name="nobat-count" min="0" id="morning-patients-${day}" value="${schedule.max_appointments || ''}">
                </div>
                <div class="form-group col-sm-1 position-relative">
                    <button class="btn btn-light btn-sm add-row-btn" data-day="${day}">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-plus" viewBox="0 0 24 24" height="1em" role="img">
                            <path d="M5 12h14m-7-7v14"></path>
                        </svg>
                    </button>
                </div>
                <div class="form-group col-sm-1 position-relative">
                    <button class="btn btn-light btn-sm copy-to-other-day-btn" data-toggle="modal" data-target="#checkboxModal" data-day="${day}">
                        <img src="${svgUrl}" alt="کپی">
                    </button>
                </div>
            </div>
        </div>
    `;
  $container.append(mainRowHtml);
  // اضافه کردن برنامه کاری‌های جدید
  let workHours = [];
  workHours = schedule.work_hours ? JSON.parse(schedule.work_hours) : []; // تبدیل JSON به آرایه
  if (workHours && workHours.length > 0) {
   workHours.forEach(slot => {
    const slotHtml = createSlotHtml(slot, day);
    $container.append(slotHtml);
   });
  }
  // بازسازی تایم‌پیکرها
  initializeTimepicker();
 }
 function createParentHtml(day) {
  return `
        <div class="top-details-header"><span>برنامه کاری روز ${getPersianDayName(day)} 👇   </span></div>
        <div class="form-row w-100 d-flex justify-content-between align-items-center border-bottom-2">
            <div class="d-flex justify-content-start align-items-center gap-4 mt-3 mb-4">
                <div class="form-group position-relative timepicker-ui">
                    <label for="morning-start-${day}" class="label-top-input-special-takhasos">از</label>
                    <input type="text" class="form-control h-50 timepicker-ui-input text-center font-weight-bold font-size-13" id="morning-start-${day}" value="">
                </div>
                <div class="form-group position-relative timepicker-ui">
                    <label for="morning-end-${day}" class="label-top-input-special-takhasos">تا</label>
                    <input type="text" class="form-control h-50 timepicker-ui-input text-center font-weight-bold font-size-13" id="morning-end-${day}" value="">
                </div>
                <div class="form-group col-sm-3 position-relative">
                    <label for="morning-patients-${day}" class="label-top-input-special-takhasos">تعداد نوبت</label>
                    <input type="text" readonly class="form-control h-50 text-center bg-white" name="nobat-count" min="0" id="morning-patients-${day}" data-toggle="modal" data-target="#CalculatorModal" data-day="${day}" value="">
                </div>
                <div class="form-group col-sm-1 position-relative">
                    <button class="btn btn-light btn-sm add-row-btn" data-day="${day}">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="plasmic-default__svg plasmic_all__FLoMj PlasmicWorkhours_svg__zLXoO__lsZwf lucide lucide-plus" viewBox="0 0 24 24" height="1em" role="img">
                            <path d="M5 12h14m-7-7v14"></path>
                        </svg>
                    </button>
                </div>
                <div class="form-group col-sm-1 position-relative">
                    <button class="btn btn-light btn-sm copy-to-other-day-btn" data-toggle="modal" data-target="#checkboxModal" data-day="${day}">
                        <img src="${svgUrl}">
                    </button>
                </div>
            </div>
        </div>
    `;
 }
 $(document).on('hidden.bs.modal', '#checkboxModal', function() {
  // پاکسازی کامل وضعیت مدال و حذف backdrop
  $(this).find('input[type="checkbox"]').prop('checked', false); // ریست چک‌باکس‌ها
  $('.modal-backdrop').remove();
  $('body').removeClass('modal-open'); // اطمینان از عدم باقی‌ماندن کلاس
 });
 $(document).on('click', '.copy-to-other-day-btn', function(e) {
  e.preventDefault(); // جلوگیری از رفتار پیش‌فرض
  const $button = $(this);
  $('#saveSingleSlotSelection').attr('id', 'saveSelection');
  // غیرفعال کردن موقت دکمه برای جلوگیری از کلیک‌های مکرر
  $button.prop('disabled', true);
  setTimeout(() => {
   $button.prop('disabled', false); // دوباره فعال کردن دکمه بعد از 1 ثانیه
  }, 1000);
  $('#checkboxModal').modal('show');
 });
 // تابع بارگذاری برنامه کاری‌ها
 function loadDaySlots(day, callback) {
  $.ajax({
   url: "{{ route('dr-get-work-schedule') }}",
   method: 'GET',
   success: function(response) {
    const daySchedule = response.workSchedules.find(schedule => schedule.day === day);
    if (daySchedule && daySchedule.slots) {
     const $container = $(`#morning-${day}-details`);
     // حذف تمام ردیف‌های قبلی به جز اولین
     $container.find('.form-row:not(:first)').remove();
     daySchedule.slots.forEach(function(slot) {
      const slotHtml = createSlotHtml(slot, day);
      $container.append(slotHtml);
     });
    }
    if (callback) callback();
   },
   error: function(xhr) {}
  });
 }
 $(document).on('click', '.copy-single-slot-btn', function() {
  const $button = $(this);
  /*  saveSingleSlotSelection */
  const slotId = $button.closest('.form-row').data('slot-id');
  const startTime = $button.data('start-time');
  const endTime = $button.data('end-time');
  const maxAppointments = $button.data('max-appointments');
  const currentDay = $button.data('day');
  // ریست و مخفی کردن چک‌باکس روز جاری
  $('input[type="checkbox"][id$="-copy-modal"]').prop('checked', false);
  $('input[type="checkbox"][id$="-copy-modal"]').each(function() {
   const dayId = $(this).attr('id');
   if (dayId === `${currentDay}-copy-modal`) {
    $(this).closest('div').removeClass('d-flex').css('display', 'none');
   } else {
    $(this).closest('div').addClass('d-flex').css('display', 'flex');
   }
  });
  // باز کردن مدال
  $('#checkboxModal').modal('show');
  // ذخیره‌سازی اطلاعات برنامه کاری در دکمه ذخیره
  $('#saveSelection').data('slot-id', slotId);
  $('#saveSelection').data('source-day', currentDay);
  $('#saveSelection').attr('id', 'saveSingleSlotSelection').data('slot-id', slotId)
   .data('source-day', currentDay)
   .data('start-time', startTime)
   .data('end-time', endTime)
   .data('max-appointments', maxAppointments);
 });
 $(document).on('click', '#saveSingleSlotSelection', function() {
  // جلوگیری از کلیک مکرر
  const $button = $(this);
  if ($button.data('submitting')) {
   return; // اگر در حال ارسال است، خروج
  }
  $button.data('submitting', true); // تنظیم فلگ
  const slotId = $(this).data('slot-id');
  const startTime = $button.data('start-time');
  const endTime = $button.data('end-time');
  const maxAppointments = $button.data('max-appointments');
  const sourceDay = $(this).data('source-day');
  const targetDays = [];
  // جمع‌آوری روزهای انتخاب‌شده
  $('#checkboxModal input[type="checkbox"]:checked').each(function() {
   if ($(this).attr('id') !== 'select-all-copy-modal') {
    targetDays.push($(this).attr('id').replace('-copy-modal', ''));
   }
  });
  if (targetDays.length === 0) {
   toastr.error('لطفاً حداقل یک روز را انتخاب کنید')
   $button.data('submitting', false); // بازنشانی فلگ
   return;
  }
  $.ajax({
   url: "{{ route('copy-single-slot') }}",
   method: 'POST',
   data: {
    source_day: sourceDay,
    target_days: targetDays,
    slot_id: slotId,
    start_time: startTime,
    end_time: endTime,
    max_appointments: maxAppointments,
    override: 0,
    _token: '{{ csrf_token() }}'
   },
   complete: function() {
    // در هر صورت فلگ را بازنشانی کنید
    $button.data('submitting', false);
   },
   success: function(response) {
    response.target_days.forEach(function(day) {
     const dayCheckbox = $(`#${day}`);
     if (!dayCheckbox.is(':checked')) {
      dayCheckbox.prop('checked', true).trigger('change');
     }
     $(`.work-hours-${day}`).removeClass('d-none');
    });
    toastr.success('برنامه با موفقیت کپی شد');
    $("#checkboxModal").modal("hide"); // بستن مدال
    $("#checkboxModal").removeClass("show");
    $(".modal-backdrop").remove();
    loadWorkSchedule(response)
    // به‌روزرسانی UI برای روزهای مقصد
    response.target_days.forEach(function(day) {
     // 1. فعال کردن چک‌باکس مربوط به روز مقصد
     const dayCheckbox = $(`#${day}`);
     dayCheckbox.prop('checked', true);
     // 2. نمایش بخش ساعات کاری مربوط به روز مقصد
     $(`.work-hours-${day}`).removeClass('d-none');
     reloadDayData(day);
     loadWorkSchedule(response)
    });
   },
   error: function(xhr) {
    if (xhr.status === 400 && xhr.responseJSON.conflicting_slots) {
     const conflictingSlots = xhr.responseJSON.conflicting_slots;
     let conflictMessage = 'بازه‌های زمانی زیر تداخل دارند:<ul>';
     conflictingSlots.forEach(slot => {
      conflictMessage += `<li>روز ${slot.day}: ${slot.start} - ${slot.end}</li>`;
     });
     conflictMessage += '</ul> آیا مایل به جایگزینی هستید؟';
     Swal.fire({
      title: 'تداخل بازه‌های زمانی',
      html: conflictMessage,
      icon: 'warning',
      showCancelButton: true,
      confirmButtonText: 'بله، جایگزین شود',
      cancelButtonText: 'خیر',
      reverseButtons: true,
     }).then((result) => {
      if (result.isConfirmed) {
       // ارسال درخواست برای جایگزینی
       $.ajax({
        url: "{{ route('copy-single-slot') }}",
        method: 'POST',
        data: {
         source_day: sourceDay,
         target_days: targetDays,
         slot_id: slotId,
         start_time: startTime,
         end_time: endTime,
         max_appointments: maxAppointments,
         override: 1,
         _token: '{{ csrf_token() }}'
        },
        complete: function() {
         // بازنشانی فلگ
         $button.data('submitting', false);
        },
        success: function(response) {
         // به‌روزرسانی UI
         response.target_days.forEach(function(day) {
          // 1. فعال کردن چک‌باکس مربوط به روز مقصد
          const dayCheckbox = $(`#${day}`);
          dayCheckbox.prop('checked', true);
          // 2. نمایش بخش ساعات کاری مربوط به روز مقصد
          $(`.work-hours-${day}`).removeClass('d-none');
          reloadDayData(day);
          loadWorkSchedule(response)
         });
         toastr.success('برنامه کاری با موفقیت جایگزین شد.');
         $("#checkboxModal").modal("hide"); // بستن مدال
         $("#checkboxModal").removeClass("show");
         $(".modal-backdrop").remove();
         loadWorkSchedule(response)
        },
        error: function(xhr) {
         toastr.error(xhr.responseJSON?.message || 'خطا در جایگزینی برنامه کاری');
        },
       });
      } else {
       toastr.warning('عملیات لغو شد.');
      }
     });
    } else {
     toastr.error(xhr.responseJSON?.message || 'خطا در عملیات');
    }
   }
  });
 });
 // تابع ایجاد HTML برای برنامه کاری
 function createCopySlotHtml(slot) {
  const start_time = slot?.time_slots?.start_time || "08:00";
  const end_time = slot?.time_slots?.end_time || "12:00";
  const max_appointments = slot?.max_appointments || '';
  const day = slot?.day || "sunday"; // مقدار پیش‌فرض
  const slotId = slot?.id || "";
  // تولید HTML با ورودی‌های تابع
  return `
    <div class="mt-3 form-row d-flex justify-content-between w-100 p-3 bg-active-slot border-radius-4" data-slot-id="${slotId || ''}">
      <div class="d-flex justify-content-start align-items-center gap-4">
        <div class="form-group position-relative timepicker-ui">
          <label class="label-top-input-special-takhasos">از</label>
          <input type="text" class="form-control h-50 timepicker-ui-input text-center font-weight-bold font-size-13 start-time bg-white" value="${start_time}" readonly>
        </div>
        <div class="form-group position-relative timepicker-ui">
          <label class="label-top-input-special-takhasos">تا</label>
          <input type="text" class="form-control h-50 timepicker-ui-input text-center font-weight-bold font-size-13 end-time bg-white" value="${end_time}" readonly>
        </div>
        <div class="form-group col-sm-3 position-relative">
          <label class="label-top-input-special-takhasos">تعداد نوبت</label>
          <input type="text" class="form-control h-50 text-center max-appointments bg-white" value="${max_appointments}" readonly>
        </div>
         <div class="form-group col-sm-1 position-relative">
            <button class="btn btn-light btn-sm copy-single-slot-btn" data-toggle="modal" data-target="#checkboxModal" data-day="${day}" data-start-time="${start_time}" data-end-time="${end_time}" data-max-appointments="${max_appointments}" data-slot-id="${slotId}">
                <img src="${svgUrl}">
            </button>
          </div>
        <div class="form-group col-sm-2 position-relative">
          <button class="btn btn-light btn-sm remove-row-btn" data-slot-id="${slotId || ''}" data-start-time="${start_time}" data-end-time="${end_time}" data-max-appointments="${max_appointments}" data-day="${day}">
            <img src="${trashSvg}">
          </button>
        </div>
      </div>
      <div class="d-flex align-items-center">
        <button type="button" class="btn btn-primary btn-sm h-50" 
          data-toggle="modal" 
          data-target="#scheduleModal" 
          data-day="${day}" data-start-time="${start_time}" data-end-time="${end_time}" data-max-appointments="${max_appointments}">
          برنامه باز شدن نوبت‌ها
        </button>
      </div>
    </div>
  `;
 }
 $(document).ready(function() {
  // برای بازگرداندن حالت اولیه مدال
  $(document).on('hidden.bs.modal', '#checkboxModal', function() {
   // نمایش مجدد همه چک‌باکس‌ها
   $('input[type="checkbox"][id$="-copy-modal"]').closest('div').show();
  });
 });
 // هنگام اضافه کردن برنامه کاری ج
 // هنگام کپی کردن
 function initializeMainElement(day) {
  const $mainElement = $(`#morning-${day}-details .form-row:first`);
  const startTime = $mainElement.find('.start-time').val() || '';
  const endTime = $mainElement.find('.end-time').val() || '';
  const maxAppointments = $mainElement.find('.max-appointments').val() || 1;
  $(`#morning-start-${day}`).val(startTime);
  $(`#morning-end-${day}`).val(endTime);
  $(`#morning-patients-${day}`).val(maxAppointments);
 }
 // تابع بارگذاری داده‌های سرور
 function loadWorkSchedule(response) {
  try {
   // بازسازی المان اصلی برای هر روز
   response.workSchedules.forEach(function(schedule) {
    $(`#${schedule.day}`).prop('checked', schedule.is_working);
    if (schedule.is_working) {
     $(`.work-hours-${schedule.day}`).removeClass('d-none');
     const mainRowHtml = createMainRowHtml(schedule.day);
     $(`#morning-${schedule.day}-details`).html(mainRowHtml);
    } else {
     $(`.work-hours-${schedule.day}`).addClass('d-none');
    }
    // بارگذاری برنامه کاری‌ها
    if (schedule.slots && schedule.slots.length > 0) {
     const $container = $(`#morning-${schedule.day}-details`);
     schedule.slots.forEach(function(slot) {
      const newRow = createSlotHtml(slot, schedule.day);
      $container.append(newRow);
     });
    }
   });
   // تنظیم مقادیر کانفیگ
   if (response.appointmentConfig) {
    $('#appointment-toggle').prop('checked', response.appointmentConfig.auto_scheduling);
    $('input[name="calendar_days"]').val(response.appointmentConfig.calendar_days || 30);
    $('#posible-appointments').prop('checked', response.appointmentConfig.online_consultation);
    $('#posible-appointments-inholiday').prop('checked', response.appointmentConfig.holiday_availability);
   }
   // مجدداً راه‌اندازی تایم پیکرها
  } catch (error) {}
 }
 // تابع ایجاد ردیف اصلی
 function createMainRowHtml(day) {
  return `
    <div class="top-details-header"><span>برنامه کاری روز ${getPersianDayName(day)} 👇   </span></div>
    <div class="form-row w-100 d-flex justify-content-between align-items-center border-bottom-2">
      <div class="d-flex justify-content-start align-items-center gap-4 mt-3 mb-4">
        <div class="form-group position-relative timepicker-ui">
          <label for="morning-start-${day}" class="label-top-input-special-takhasos">از</label>
          <input type="text" class="form-control h-50 timepicker-ui-input text-center font-weight-bold font-size-13" id="morning-start-${day}" value="">
        </div>
        <div class="form-group position-relative timepicker-ui">
          <label for="morning-end-${day}" class="label-top-input-special-takhasos">تا</label>
          <input type="text" class="form-control h-50 timepicker-ui-input text-center font-weight-bold font-size-13" id="morning-end-${day}" value="">
        </div>
        <div class="form-group col-sm-3 position-relative">
          <label for="morning-patients-${day}" class="label-top-input-special-takhasos">تعداد نوبت</label>
          <input type="text" readonly class="form-control h-50 text-center bg-white" name="nobat-count" min="0" id="morning-patients-${day}" data-toggle="modal" data-target="#CalculatorModal" data-day="${day}" value="">
        </div>
        <div class="form-group col-sm-1 position-relative">
          <button class="btn btn-light btn-sm add-row-btn" data-day="${day}">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="plasmic-default__svg plasmic_all__FLoMj PlasmicWorkhours_svg__zLXoO__lsZwf lucide lucide-plus" viewBox="0 0 24 24" height="1em" role="img"><path d="M5 12h14m-7-7v14"></path></svg>
          </button>
        </div>
        <div class="form-group col-sm-1 position-relative">
          <button class="btn btn-light btn-sm copy-to-other-day-btn" data-toggle="modal" data-target="#checkboxModal" data-day="${day}">
            <img src="${svgUrl}">
          </button>
        </div>
      </div>
    </div>
  `;
 }
 // تابع ایجاد ردیف برنامه کاری
 function createSlotHtml(slot, day) {  
  const startTime = slot.start ?? '';
  const endTime = slot.end ?? '';
  const maxAppointments = slot.max_appointments || '';
  return `
    <div class="mt-3 form-row d-flex justify-content-between w-100 p-3 bg-active-slot border-radius-4" data-slot-id="${slot.id}">
      <div class="d-flex justify-content-start align-items-center gap-4">
        <div class="form-group position-relative timepicker-ui">
          <label class="label-top-input-special-takhasos">از</label>
          <input type="text" class="form-control h-50 timepicker-ui-input text-center font-weight-bold font-size-13 start-time bg-white" value="${startTime}" readonly>
        </div>
        <div class="form-group position-relative timepicker-ui">
          <label class="label-top-input-special-takhasos">تا</label>
          <input type="text" class="form-control h-50 timepicker-ui-input text-center font-weight-bold font-size-13 end-time bg-white" value="${endTime}" readonly>
        </div>
        <div class="form-group col-sm-3 position-relative">
          <label class="label-top-input-special-takhasos">تعداد نوبت</label>
          <input type="text" class="form-control h-50 text-center max-appointments bg-white" value="${maxAppointments}" readonly>
        </div>
         <div class="form-group col-sm-1 position-relative">
             <button class="btn btn-light btn-sm copy-single-slot-btn" data-toggle="modal" data-target="#checkboxModal" data-day="${day}" data-start-time="${startTime}" data-end-time="${endTime}" data-max-appointments="${maxAppointments}" data-slot-id="${slot.id}">
              <img src="${svgUrl}">
             </button>
          </div>
        <div class="form-group col-sm-2 position-relative">
          <button class="btn btn-light btn-sm remove-row-btn" data-slot-id="${slot.id}" data-start-time="${startTime}" data-end-time="${endTime}" data-max-appointments="${maxAppointments}" data-day="${day}">
            <img src="${trashSvg}">
          </button>
        </div>
      </div>
      <div class="d-flex align-items-center">
        <button type="button" class="btn btn-primary btn-sm h-50" 
          data-toggle="modal" 
          data-target="#scheduleModal" 
          data-day="${day}" data-start-time="${startTime}" data-end-time="${endTime}" data-max-appointments="${maxAppointments}">
          برنامه باز شدن نوبت‌ها
        </button>
      </div>
    </div>
  `;
 }
 // استفاده از کش
 $(document).ready(function() {
  // اگر کش وجود ندارد، داده‌ها را از سرور بارگذاری کنید
  $.ajax({
   url: "{{ route('dr-get-work-schedule') }}",
   method: 'GET',
   success: function(response) {
    loadWorkSchedule(response); // بارگذاری داده‌ها
   }
  });
 });
 $(document).on('click', '.copy-to-other-day-btn', function() {
  const currentDay = $(this).data('day');
  // ابتدا همه چک‌باکس‌ها را ریست کنید
  $('input[type="checkbox"][id$="-copy-modal"]').prop('checked', false);
  // چک باکس انتخاب همه را هم ریست کنید
  $('#select-all-copy-modal').prop('checked', false);
  // مخفی کردن چک‌باکس روز جاری
  $('input[type="checkbox"][id$="-copy-modal"]').each(function() {
   const dayId = $(this).attr('id');
   if (dayId === `${currentDay}-copy-modal`) {
    $(this).closest('div').removeClass('d-flex').css('display', 'none');
   } else {
    $(this).closest('div').addClass('d-flex').css('display', 'flex');
   }
  });
 });
 // برای آیکون کپی
 $(document).ready(function() {
  // اگر آیکون کپی کار نمی‌کند، مطمئن شوید که SVG درست لینک شده است
  $('.copy-to-other-day-btn').each(function() {
   $(this).html(`<img src="${svgUrl}" alt="کپی">`);
  });
  // در زمان بستن مدال، بازگرداندن حالت اولیه
  $(document).on('hidden.bs.modal', '#checkboxModal', function() {
   // بازگرداندن نمایش تمام روزها
   $('input[type="checkbox"][id$="-copy-modal"]').each(function() {
    $(this).closest('div').addClass('d-flex').css('display', 'flex');
   });
   // ریست کردن چک‌باکس‌ها
   $('input[type="checkbox"][id$="-copy-modal"]').prop('checked', false);
   $('#select-all-copy-modal').prop('checked', false);
  });
 });
 function setupModalButtons() {
  // لودر برای همه مدال‌ها
  $('[data-modal-submit]').on('click', function() {
   const $button = $(this);
   const $loader = $button.find('.loader');
   const $buttonText = $button.find('.button_text');
   $buttonText.hide();
   $loader.show();
   // عملیات AJAX
   $.ajax({
    // تنظیمات درخواست
    complete: function() {
     $buttonText.show();
     $loader.hide();
    }
   });
  });
 }
 // فراخوانی تابع برای تنظیم دکمه‌های مدال
 $(document).ready(setupModalButtons);
 $(document).on('click', '.add-row-btn', function() {
  const day = $(this).data('day');
  const $container = $(`#morning-${day}-details`);
  const startTime = $(`#morning-start-${day}`).val();
  const endTime = $(`#morning-end-${day}`).val();
  const maxAppointments = $(`#morning-patients-${day}`).val() || 1;
  $.ajax({
   url: "{{ route('save-time-slot') }}",
   method: 'POST',
   data: {
    day: day,
    start_time: startTime,
    end_time: endTime,
    max_appointments: maxAppointments,
    _token: '{{ csrf_token() }}'
   },
   success: function(response) {
    const newRow = `
                <div class="mt-3 form-row d-flex justify-content-between w-100 p-3 bg-active-slot border-radius-4 align-items-center border-radius-4" data-slot-id="${response.slot_id}">
                    <div class="d-flex justify-content-start align-items-center gap-4">
                        <div class="form-group position-relative timepicker-ui">
                            <label class="label-top-input-special-takhasos">از</label>
                            <input type="text" class="form-control h-50 timepicker-ui-input  text-center font-weight-bold font-size-13 start-time bg-white" value="${startTime}" readonly>
                        </div>
                        <div class="form-group position-relative timepicker-ui">
                            <label class="label-top-input-special-takhasos">تا</label>
                            <input type="text" class="form-control h-50 timepicker-ui-input text-center font-weight-bold font-size-13 end-time bg-white" value="${endTime}" readonly>
                        </div>
                        <div class="form-group col-sm-3 position-relative">
                            <label class="label-top-input-special-takhasos">تعداد نوبت</label>
                            <input type="text" class="form-control h-50 text-center max-appointments bg-white" value="${maxAppointments}" readonly>
                        </div>
                        <div class="form-group col-sm-1 position-relative">
                           <button class="btn btn-light btn-sm copy-single-slot-btn" data-toggle="modal" data-target="#checkboxModal" data-day="${day}" data-start-time="${startTime}" data-end-time="${endTime}" data-max-appointments="${maxAppointments}" data-slot-id="${response.slot_id}">
                             <img src="${svgUrl}">
                           </button>
                        </div>
                        <div class="form-group col-sm-2 position-relative">
                            <button class="btn btn-light btn-sm remove-row-btn" data-slot-id="${response.slot_id}" data-start-time="${startTime}" data-end-time="${endTime}" data-max-appointments="${maxAppointments}" data-day="${day}">
                                <img src="${trashSvg}">
                            </button>
                        </div>
                    </div>
                    <div class="d-flex align-items-center">
                        <button type="button" class="btn btn-primary btn-sm h-50" data-toggle="modal" data-target="#scheduleModal" data-day="${day}" data-start-time="${startTime}" data-end-time="${endTime}" data-max-appointments="${maxAppointments}">برنامه باز شدن نوبت‌ها</button>
                    </div>
                </div>
            `;
    $container.append(newRow);
    initializeTimepicker();
    toastr.success('موفقیت آمیز');
   },
   error: function(xhr) {
    toastr.error(xhr.responseJSON.message);
   }
  });
 });
 $(document).on('click', '[data-target="#scheduleModal"]', function() {
  const day = $(this).data('day');
  const start_time = $(this).data('start-time')
  const end_time = $(this).data('end-time')
  const max_appointments = $(this).data('max-appointments')
  $('#scheduleModal').data('currentDay', day); // ذخیره روز جاری در مدال
  $("#saveSchedule").attr('data-day', day);
  checkAllDaysSettings(day, start_time, end_time, max_appointments);
  const persianDay = getPersianDayName(day);
  const modal = $('#scheduleModal');
  // افزودن اتریبیوت data-max-appointments و مقداردهی
  modal.attr('data-max-appointments', $(this).data('max-appointments') || 0);
  modal.attr('data-day', $(this).data('day'));
  // به‌روزرسانی عنوان مدال با اطلاعات دقیق برنامه کاری
  $("#scheduleModalLabel").text(
   `برنامه زمانبندی برای نوبت های ${persianDay} ${start_time} الی ${end_time} (${max_appointments} نوبت)`
  );
  // تنظیم مقادیر پیش‌فرض برای مدال
  $('#schedule-start').val(start_time);
  $('#schedule-end').val(end_time);
  $('.setting-item').remove();
  $('.not-appointment-found').remove();
  // پاک کردن چک‌باکس‌های قبلی
  $('input[type="checkbox"][id$="-copy-modal"]').prop('checked', false);
  // چک کردن روز جاری
  $(`#${day}-copy-modal`).prop('checked', true);
  $.ajax({
   url: "{{ route('get-appointment-settings') }}",
   method: 'GET',
   data: {
    day: day,
    start_time: start_time,
    end_time: end_time,
    max_appointments: max_appointments,
   },
   success: function(response) {
    
    // حذف لیست‌های قبلی
    if (response.status && response.settings) {
      
     // تبدیل تنظیمات JSON به آرایه
     const settings = response.settings;

     // فیلتر تنظیمات مرتبط با برنامه کاری جاری
    
     
     if (settings.length > 0) {
      let settingsListHtml = '<div class="mt-3 settings-list">';
      const dayMapFa = {
       'saturday': 'شنبه',
       'sunday': 'یکشنبه',
       'monday': 'دوشنبه',
       'tuesday': 'سه‌شنبه',
       'wednesday': 'چهارشنبه',
       'thursday': 'پنج‌شنبه',
       'friday': 'جمعه'
      };
      // ساخت HTML برای تنظیمات فیلتر شده
      
      settings.forEach(setting => {
        
       settingsListHtml += `
            <div class="d-flex justify-content-between align-items-center border-bottom p-2 border-radius-4 mb-2 setting-item mt-2 bg-active-slot" data-day="${response.day}" data-selected-day="${setting.selected_day}">
              <span class="font-weight-bold text-success p-2">
                 باز شدن نوبت‌ها از ${setting.start_time} تا ${setting.end_time} روز ${dayMapFa[setting.selected_day]}
              </span>
              <button class="btn btn-sm btn-light delete-schedule-setting" 
                      data-day="${response.day}" 
                      data-start-time="${setting.start_time}" 
                      data-end-time="${setting.end_time}" data-day="${day}" data-selected-day="${setting.selected_day}">
                <img src="${trashSvg}">
              </button>
            </div>`;
      });
      settingsListHtml += '</div>';
      $('#scheduleModal .modal-body').append(settingsListHtml);
     } else {
      // اگر تنظیمات مرتبط پیدا نشد
      $('#scheduleModal .modal-body').append(
       '<div class="mt-3 font-weight-bold settings-list text-danger text-center not-appointment-found">هیچ برنامه ای یافت نشد.</div>'
      );
     }
    }
   },
   error: function(xhr) {
    console.error('خطا در دریافت تنظیمات:', xhr);
   }
  });
  $(document).on('click', '.badge-time-styles-day', function() {
   $('.badge-time-styles-day').removeClass('active-hover');
   const dayEn = $(this).data('day');
   $(this).addClass('active-hover');
   // بررسی تنظیمات برای روز انتخاب‌شده
  });
  checkAllDaysSettings(day, start_time, end_time, max_appointments);
 });
 // تابع تبدیل نام روز به فارسی (اگر قبلاً تعریف نشده باشد)
 function addNewRow(day) {
  const newRow = `
        <div class="mt-3 form-row d-flex justify-content-between w-100 p-3 bg-active-slot border-radius-4">
            <div class="d-flex justify-content-start align-items-center gap-4">
                <div class="form-group position-relative timepicker-ui">
                    <label class="label-top-input-special-takhasos">از</label>
                    <input type="text" class="form-control h-50 timepicker-ui-input text-center font-weight-bold font-size-13 start-time bg-white" value="">
                </div>
                <div class="form-group position-relative timepicker-ui">
                    <label class="label-top-input-special-takhasos">تا</label>
                    <input type="text" class="form-control h-50 timepicker-ui-input text-center font-weight-bold font-size-13 end-time bg-white" value="">
                </div>
                <div class="form-group col-sm-3 position-relative">
                    <label class="label-top-input-special-takhasos">تعداد نوبت</label>
                    <input type="text" name="max-appointments" class="form-control h-50 text-center max-appointments bg-white" min="0" value="" readonly>
                </div>
                <div class="form-group col-sm-2 position-relative">
                    <button class="btn btn-light btn-sm remove-row-btn">
                        <img src="${trashSvg}">
                    </button>
                </div>
            </div>
        </div>
    `;
  const $container = $(`#morning-${day}-details`);
  $container.append(newRow);
 }
 $(document).on('click', '.remove-row-btn', function() {
  const slotId = $(this).data('slot-id');
  const start_time = $(this).data('start-time');
  const end_time = $(this).data('end-time');
  const max_appointments = $(this).data('max-appointments');
  const day = $(this).data('day');
  
  Swal.fire({
   title: 'آیا مطمئن هستید؟',
   text: "این عمل قابل بازگشت نیست!",
   icon: 'warning',
   showCancelButton: true,
   confirmButtonColor: '#3085d6',
   cancelButtonColor: '#d33',
   confirmButtonText: 'بله، حذف شود!',
   cancelButtonText: 'لغو'
  }).then((result) => {
   if (result.isConfirmed) {
    $.ajax({
     url: "{{ route('appointment.slots.destroy', ':id') }}".replace(':id', slotId),
     method: 'DELETE',
     data: {
      _token: '{{ csrf_token() }}',
      day:day,
      start_time:start_time,
      end_time:end_time,
      max_appointments:max_appointments,

     },
     success: function(response) {
      $(`[data-slot-id="${slotId}"]`).remove();
      toastr.success('حذف موفقیت آمیز');
     },
     error: function(xhr) {
      toastr.error('خطا در حذف ');
     }
    });
   }
  });
 });
 $(document).ready(function() {
  // تابع ذخیره‌سازی برنامه کاری
  function saveWorkSchedule() {
   const data = {
    auto_scheduling: $('#appointment-toggle').is(':checked'),
    calendar_days: parseInt($('input[name="calendar_days"]').val()) || 30,
    online_consultation: $('#posible-appointments').is(':checked'),
    holiday_availability: $('#posible-appointments-inholiday').is(':checked'),
    days: {}
   };
   // جمع‌آوری اطلاعات برای هر روز
   const days = ["saturday", "sunday", "monday", "tuesday", "wednesday", "thursday", "friday"];
   days.forEach(day => {
    if ($(`#${day}`).is(':checked')) {
     const workHours = collectSlots(day);
     data.days[day] = {
      is_working: true,
      work_hours: workHours.length > 0 ? JSON.stringify(workHours) : null
     };
    }
   });
   $.ajax({
    url: "{{ route('dr-save-work-schedule') }}",
    method: 'POST',
    data: JSON.stringify(data),
    contentType: 'application/json',
    headers: {
     'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    },
    success: function(response) {
     toastr.success('تنظیمات ساعات کاری با موفقیت ذخیره شد.');
     response.workSchedules.forEach(schedule => {
      updateDayUI(schedule);
     });
    },
    error: function(xhr) {
     toastr.error(xhr.responseJSON?.message || 'خطا در ذخیره‌سازی ساعات کاری.');
    }
   });
  }
  // تابع جمع‌آوری برنامه کاری‌ها
  function collectSlots(day) {
   const slots = [];
   $(`#morning-${day}-details .form-row`).each(function() {
    const $row = $(this);
    const startTime = $row.find('.start-time').val();
    const endTime = $row.find('.end-time').val();
    const maxAppointments = $row.find('.max-appointments').val() || 1;
    // فقط اضافه کردن برنامه کاری‌هایی با زمان مشخص
    if (startTime && endTime) {
     slots.push({
      start: startTime,
      end: endTime,
      max_appointments: parseInt(maxAppointments)
     });
    }
   });
   return slots;
  }
  // گوش دادن به رویداد کلیک برای ذخیره‌سازی
  $('#save-work-schedule').on('click', saveWorkSchedule);
 });
 $(document).on('click', '.close, .btn-secondary', function() {
  $(this).closest('.modal').modal('hide');
  // بستن مدال
  $(this).removeClass("show");
  $(".modal-backdrop").remove();
 });
 $(document).ready(function() {
  // تغییر وضعیت روزهای کاری با AJAX
  $.each(["saturday", "sunday", "monday", "tuesday", "wednesday", "thursday", "friday"], function(index, day) {
   $(`#${day}`).on('change', function() {
    // تبدیل به 0 یا 1
    const isWorking = $(this).is(':checked') ? 1 : 0;
    $.ajax({
     url: "{{ route('update-work-day-status') }}",
     method: 'POST',
     data: {
      day: day,
      is_working: isWorking, // استفاده از 0 یا 1
      _token: '{{ csrf_token() }}'
     },
     dataType: 'json',
     success: function(response) {
      // نمایش بخش مربوط به روز
      if (isWorking) {
       $(`.work-hours-${day}`).removeClass('d-none');
       toastr.success(`روز ${getPersianDayName(day)} فعال شد`)
      } else {
       $(`.work-hours-${day}`).addClass('d-none');
       toastr.success(`روز ${getPersianDayName(day)} غیرفعال شد`)
      }
     },
     error: function(xhr) {
      // برگرداندن چک‌باکس به وضعیت قبلی
      $(`#${day}`).prop('checked', isWorking === 1);
      // نمایش پیغام خطا
      let errorMessage = 'خطا در تغییر وضعیت روز';
      if (xhr.responseJSON && xhr.responseJSON.errors) {
       errorMessage = Object.values(xhr.responseJSON.errors).flat().join('\n');
      } else if (xhr.responseJSON && xhr.responseJSON.message) {
       errorMessage = xhr.responseJSON.message;
      }
      toastr.error(errorMessage)
     }
    });
   });
  });
  // تابع تبدیل نام روز به فارسی
 });
 function showLoading() {
  $('#work-hours').append(`
            <div class="loading-overlay">
                <div class="spinner-border text-primary" role="status">
                    <span class="sr-only">Loading...</span>
                </div>
            </div>
        `);
 }
 function hideLoading() {
  $('.loading-overlay').remove();
 }
 $(document).ready(function() {
  $('#appointment-toggle').on('change', function() {
   // Multiple methods to ensure boolean conversion
   const isAutoSchedulingEnabled = Boolean($(this).is(':checked'));
   // Alternative: const isAutoSchedulingEnabled = $(this).prop('checked') ? true : false;
   $.ajax({
    url: "{{ route('update-auto-scheduling') }}",
    method: 'POST',
    data: {
     auto_scheduling: isAutoSchedulingEnabled ? 1 : 0, // Explicit true/false conversion
     _token: '{{ csrf_token() }}'
    },
    dataType: 'json', // Explicitly set expected response type
    success: function(response) {
     if (isAutoSchedulingEnabled) {
      toastr.success('نوبت‌دهی خودکار فعال شد');
     } else {
      toastr.error('نوبت‌دهی خودکار غیرفعال شد');
     }
    },
    error: function(xhr, status, error) {
     // Detailed error logging
     // Revert checkbox state
     $('#appointment-toggle').prop('checked', !isAutoSchedulingEnabled);
     toastr.error(xhr.responseJSON?.message || 'خطا در به‌روزرسانی تنظیمات');
    }
   });
  });
 });
 $(document).ready(function() {
  $.ajax({
   url: "{{ route('dr-get-work-schedule') }}",
   method: 'GET',
   success: function(response) {
    // بازسازی المان اصلی برای هر روز

    response.workSchedules.forEach(function(schedule) {
      

     $(`#${schedule.day}`).prop('checked', schedule.is_working);
     if (schedule.is_working) {
      $(`.work-hours-${schedule.day}`).removeClass('d-none');
      let workHours = [];
      try {
       workHours = schedule.work_hours ? JSON.parse(schedule.work_hours) : [];
      } catch (e) {
       console.error("خطا در تبدیل work_hours به JSON:", e);
      }
      
      // بازسازی کامل المان اصلی
      if (workHours && workHours.length > 0) {
       const mainRowHtml = `
       <div class="top-details-header"><span>برنامه کاری روز ${getPersianDayName(schedule.day)} 👇   </span></div>
       <div class="form-row w-100 d-flex justify-content-between align-items-center border-bottom-2">
         <div class="d-flex justify-content-start align-items-center gap-4 mt-3 mb-4">
           <div class="form-group  position-relative timepicker-ui">
             <label for="morning-start-${schedule.day}" class="label-top-input-special-takhasos">از</label>
             <input type="text" class="form-control h-50 timepicker-ui-input text-center font-weight-bold font-size-13" id="morning-start-${schedule.day}" value="">
           </div>
           <div class="form-group  position-relative timepicker-ui">
             <label for="morning-end-${schedule.day}" class="label-top-input-special-takhasos">تا</label>
             <input type="text" class="form-control h-50 timepicker-ui-input text-center font-weight-bold font-size-13" id="morning-end-${schedule.day}" value="">
           </div>
           <div class="form-group col-sm-3 position-relative">
             <label for="morning-patients-${schedule.day}" class="label-top-input-special-takhasos">تعداد نوبت</label>
             <input type="text" readonly class="form-control h-50 text-center bg-white" name="nobat-count" min="0" id="morning-patients-${schedule.day}"  data-toggle="modal" data-target="#CalculatorModal" data-day="${schedule.day}" value="">
           </div>
           <div class="form-group col-sm-1 position-relative">
             <button class="btn btn-light btn-sm add-row-btn" data-day="${schedule.day}">
               <svg xmlns="http://www.w3.org/2000/svg" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="plasmic-default__svg plasmic_all__FLoMj PlasmicWorkhours_svg__zLXoO__lsZwf lucide lucide-plus" viewBox="0 0 24 24" height="1em" role="img"><path d="M5 12h14m-7-7v14"></path></svg>
             </button>
           </div>
           <div class="form-group col-sm-1 position-relative">
             <button class="btn btn-light btn-sm copy-to-other-day-btn" data-toggle="modal" data-target="#checkboxModal" data-day="${schedule.day}">
               <img src="${svgUrl}">
             </button>
           </div>
         </div>
       </div>
      `;
       $(`#morning-${schedule.day}-details`).html(mainRowHtml);
      }
     } else {
      $(`.work-hours-${schedule.day}`).addClass('d-none');
     }
     let workHours = [];
     try {
      workHours = schedule.work_hours ? JSON.parse(schedule.work_hours) : [];
     } catch (e) {
      console.error("خطا در تبدیل work_hours به JSON:", e);
     }
     // بارگذاری برنامه کاری‌ها
     if (workHours && workHours.length > 0) {
      const $container = $(`#morning-${schedule.day}-details`);
      workHours.forEach(function(slot) {
        
       const startTime = slot.start || '08:00';
       const endTime = slot.end || '12:00';
       const maxAppointments = slot.max_appointments || '';
       
       
       const newRow = `
         <div class="mt-3 form-row d-flex justify-content-between w-100 p-3 bg-active-slot border-radius-4" data-slot-id="${schedule.id}">
           <div class="d-flex justify-content-start align-items-center gap-4">
             <div class="form-group position-relative timepicker-ui">
               <label class="label-top-input-special-takhasos">از</label>
               <input type="text" class="form-control h-50 timepicker-ui-input text-center font-weight-bold font-size-13 start-time bg-white" value="${startTime}" readonly>
             </div>
             <div class="form-group position-relative timepicker-ui">
               <label class="label-top-input-special-takhasos">تا</label>
               <input type="text" class="form-control h-50 timepicker-ui-input text-center font-weight-bold font-size-13 end-time bg-white" value="${endTime}" readonly>
             </div>
             <div class="form-group col-sm-3 position-relative">
               <label class="label-top-input-special-takhasos">تعداد نوبت</label>
               <input type="text" class="form-control h-50 text-center max-appointments bg-white" value="${maxAppointments}" readonly>
             </div>
              <div class="form-group col-sm-1 position-relative">
                  <button class="btn btn-light btn-sm copy-single-slot-btn" data-toggle="modal" data-target="#checkboxModal" data-day="${schedule.day}" data-start-time="${startTime}" data-end-time="${endTime}" data-max-appointments="${maxAppointments}" data-slot-id="${schedule.id}">
                    <img src="${svgUrl}">
                  </button>
              </div>
             <div class="form-group col-sm-2 position-relative">
               <button class="btn btn-light btn-sm remove-row-btn" data-slot-id="${schedule.id}" data-start-time="${startTime}" data-end-time="${endTime}" data-max-appointments="${maxAppointments}" data-day="${schedule.day}">
                 <img src="${trashSvg}">
               </button>
             </div>
           </div>
           <div class="d-flex align-items-center">
            <div class="d-flex align-items-center">
               <button type="button" class="btn btn-primary btn-sm h-50" data-toggle="modal" data-target="#scheduleModal" data-day="${schedule.day}" data-start-time="${startTime}"data-end-time="${endTime}" data-max-appointments="${maxAppointments}">برنامه باز شدن نوبت‌ها</button>
            </div>
           </div>
         </div>
       `;
       $container.append(newRow);
      });
     }
    });
    // تنظیم مقادیر کانفیگ
    if (response.appointmentConfig) {
     $('#appointment-toggle').prop('checked', response.appointmentConfig.auto_scheduling);
     $('input[name="calendar_days"]').val(response.appointmentConfig.calendar_days || 30);
     $('#posible-appointments').prop('checked', response.appointmentConfig.online_consultation);
     $('#posible-appointments-inholiday').prop('checked', response.appointmentConfig.holiday_availability);
    }
   },
   error: function() {
    toastr.error('خطا در بارگذاری تنظیمات');
   }
  });
 });
 $(document).ready(function() {
  // تابع ذخیره‌سازی برنامه کاری
  // تابع جمع‌آوری برنامه کاری‌ها
  function collectSlots(day) {
   const slots = [];
   $(`#morning-${day}-details .form-row`).each(function() {
    const $row = $(this);
    const startTime = $row.find('.start-time').val();
    const endTime = $row.find('.end-time').val();
    const maxAppointments = $row.find('.max-appointments').val() || 1;
    // فقط اضافه کردن برنامه کاری‌های با زمان شروع و پایان
    if (startTime && endTime) {
     slots.push({
      start_time: startTime,
      end_time: endTime,
      max_appointments: parseInt(maxAppointments)
     });
    }
   });
   return slots;
  }
  $(document).on('click', '#save-work-schedule', function() {
   const submitButton = document.getElementById("save-work-schedule")
   const loader = submitButton.querySelector('.loader');
   const buttonText = submitButton.querySelector('.button_text');
   buttonText.style.display = 'none';
   loader.style.display = 'block';
   // ارسال درخواست AJAX و پس از اتمام، نمایش دوباره متن دکمه و مخفی کردن لودر
   $.ajax({
    // تنظیمات AJAX
    success: function(response) {
     buttonText.style.display = 'block';
     loader.style.display = 'none';
    },
    error: function(xhr) {
     buttonText.style.display = 'block';
     loader.style.display = 'none';
    }
   });
  });
 });
 function getPersianDayName(day) {
  const dayNames = {
   "saturday": "شنبه",
   "sunday": "یکشنبه",
   "monday": "دوشنبه",
   "tuesday": "سه‌شنبه",
   "wednesday": "چهارشنبه",
   "thursday": "پنج‌شنبه",
   "friday": "جمعه"
  };
  return dayNames[day] || day;
 }
 function getSlotInfoForDay(day) {
  const startTime = $(`#morning-start-${day}`).val() || '';
  const endTime = $(`#morning-end-${day}`).val() || '';
  const appointments = $(`#morning-patients-${day}`).val() || 1;
  return {
   startTime,
   endTime,
   appointments
  };
 }
 //appointments code
 $(document).ready(function() {
  const days = [
   "saturday", "sunday", "monday", "tuesday",
   "wednesday", "thursday", "friday"
  ];
  // تبدیل نام روز به فارسی
  var workHoursHtml = "";
  $.each(days, function(index, day) {
   workHoursHtml += `
      <div class="work-hours-${day} d-none position-relative">
        <div class="border p-3 mt-3 border-radius-4">
          <h6>${day === "saturday" ? "شنبه" : day === "sunday" ? "یکشنبه" : day === "monday" ? "دوشنبه" : day === "tuesday" ? "سه‌شنبه" : day === "wednesday" ? "چهارشنبه" : day === "thursday" ? "پنج‌شنبه" : "جمعه"}</h6>
          <div class="d-flex mt-2 justify-content-start my-copy-item">
            <div class="dropdown-menu dropdown-menu-right" aria-labelledby="dropdownMenuButton">
              ${days
                .map((otherDay) => {
                  if (otherDay !== day) {
                    return `<a class="dropdown-item" href="#" data-day="${otherDay}">${otherDay === "saturday" ? "شنبه" : otherDay === "sunday" ? "یکشنبه" : otherDay === "monday" ? "دوشنبه" : otherDay === "tuesday" ? "سه‌شنبه" : otherDay === "wednesday" ? "چهارشنبه" : otherDay === "thursday" ? "پنج‌شنبه" : "جمعه"}</a>`;
                  }
                })
                .join("")}
            </div>
          </div>
          <div id="morning-${day}-details" class="mt-4">
            <div class="top-details-header"><span>برنامه کاری روز ${getPersianDayName(day)} 👇   </span></div>
            <div class="form-row w-100 d-flex justify-content-between align-items-center border-bottom-2">
              <div class="d-flex justify-content-start align-items-center gap-4 mt-3 mb-4">
                <div class="form-group  position-relative timepicker-ui">
                <label for="morning-start-${day}" class="label-top-input-special-takhasos">از</label>
                <input type="text" class="form-control h-50 timepicker-ui-input text-center font-weight-bold font-size-13" id="morning-start-${day}" value="">
              </div>
              <div class="form-group  position-relative timepicker-ui">
                <label for="morning-end-${day}" class="label-top-input-special-takhasos">تا</label>
                <input type="text" class="form-control h-50 timepicker-ui-input text-center font-weight-bold font-size-13" id="morning-end-${day}" value="">
              </div>
              <div class="form-group col-sm-3 position-relative">
                <label for="morning-patients-${day}" class="label-top-input-special-takhasos">تعداد نوبت</label>
                <input type="text" readonly class="form-control h-50 text-center bg-white" name="nobat-count" min="0" id="morning-patients-${day}"  data-toggle="modal" data-target="#CalculatorModal" data-day="${day}">
              </div>
              <div class="form-group col-sm-1 position-relative">
                <button class="btn btn-light btn-sm add-row-btn" data-day="${day}">
                  <svg xmlns="http://www.w3.org/2000/svg" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="plasmic-default__svg plasmic_all__FLoMj PlasmicWorkhours_svg__zLXoO__lsZwf lucide lucide-plus" viewBox="0 0 24 24" height="1em" role="img"><path d="M5 12h14m-7-7v14"></path></svg>
                </button>
              </div>
              <div class="form-group col-sm-1 position-relative">
                <button class="btn btn-light btn-sm copy-to-other-day-btn" data-toggle="modal" data-target="#checkboxModal" data-day="${day}">
                  <img src="${svgUrl}">
                </button>
              </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    `;
  });
  $("#work-hours").html(workHoursHtml);
  // Function to add a new row
  function addNewRow(day) {
   const newRow = `
      <div class="mt-3 form-row d-flex justify-content-between w-100 p-3 bg-active-slot border-radius-4">
        <div class="d-flex justify-content-start align-items-center gap-4">
          <div class="form-group position-relative timepicker-ui">
            <label class="label-top-input-special-takhasos">از</label>
            <input type="text" class="form-control h-50 timepicker-ui-input text-center font-weight-bold font-size-13 start-time bg-white" value="">
          </div>
          <div class="form-group position-relative timepicker-ui">
            <label class="label-top-input-special-takhasos">تا</label>
            <input type="text" class="form-control h-50 timepicker-ui-input text-center font-weight-bold font-size-13 end-time bg-white" value="">
          </div>
          <div class="form-group col-sm-3 position-relative">
            <label class="label-top-input-special-takhasos">تعداد نوبت</label>
            <input type="text" name="max-appointments" class="form-control h-50 text-center max-appointments bg-white" min="0" value="" readonly>
          </div>
          <div class="form-group col-sm-2 position-relative">
            <button class="btn btn-light btn-sm remove-row-btn">
              <img src="${trashSvg}">
            </button>
          </div>
        </div>
        <div class="d-flex align-items-center">
          <div class="d-flex align-items-center">
              <button type="button" class="btn btn-primary btn-sm h-50" data-toggle="modal" data-target="#scheduleModal" data-day="${day}">برنامه باز شدن نوبت‌ها</button>
          </div>
        </div>
      </div>
    `;
   const $container = $(`#morning-${day}-details`);
   const $newRow = $(newRow);
   // اضافه کردن ردیف جدید
   $container.append($newRow);
   // تنظیم timepicker برای ورودی‌های جدید
   const newTimeInputs = $newRow.find('.timepicker-ui-input');
   newTimeInputs.each(function() {
    const newTimepicker = new window.tui.TimepickerUI(this, {
     clockType: '24h',
     theme: 'basic',
     mobile: 'true',
     enableScrollbar: 'true'
    });
    newTimepicker.create();
   });
  }
  // Manage select all checkbox
  $("#selectAll").on("change", function() {
   var isChecked = $(this).is(":checked");
   $('input[type="checkbox"]').not(this).prop("checked", isChecked);
  });
  // Save selection
  // Event listeners for adding and removing rows
  $.each(days, function(index, day) {
   $("#" + day).on("change", function() {
    if ($(this).is(":checked")) {
     $(".work-hours-" + day).removeClass("d-none");
    } else {
     $(".work-hours-" + day).addClass("d-none");
    }
   });
  });
 });
 // در زمان انتخاب روز در مدال
 function checkAllDaysSettings(day, startTime, endTime, maxAppointments) {
  $.ajax({
   url: "{{ route('get-all-days-settings') }}",
   method: 'GET',
   data: {
    day: day,
    start_time: startTime,
    end_time: endTime,
    max_appointments: maxAppointments
   },
   success: function(response) {
    if (response.status && response.settings) {
     let settingsListHtml = '<div class="mt-3 settings-list">';
     // اضافه کردن کلاس اکتیو به روزهایی که تنظیم دارند
     response.settings.forEach(function(setting) {
      // نقشه تبدیل روز انگلیسی به فارسی
      const dayMapEn = {
       'saturday': 'شنبه',
       'sunday': 'یکشنبه',
       'monday': 'دوشنبه',
       'tuesday': 'سه‌شنبه',
       'wednesday': 'چهارشنبه',
       'thursday': 'پنج‌شنبه',
       'friday': 'جمعه'
      };
      // اضافه کردن کلاس اکتیو به روز مربوطه
      $(`.badge-time-styles-day:contains('${dayMapEn[setting.day]}')`)
       .addClass('active-hover');
      if (setting.start_time && setting.end_time) {
       const dayMapFa = {
        'saturday': 'شنبه',
        'sunday': 'یکشنبه',
        'monday': 'دوشنبه',
        'tuesday': 'سه‌شنبه',
        'wednesday': 'چهارشنبه',
        'thursday': 'پنج‌شنبه',
        'friday': 'جمعه'
       };
       settingsListHtml += `
              <div class="d-flex justify-content-between align-items-center border-bottom p-2 border-radius-4  mb-2 setting-item bg-active-slot" data-day="${setting.day}" data-selected-day="${setting.selected_day}">
                <span class="font-weight-bold text-success p-2">
                    باز شدن نوبت‌ها از ${setting.start_time} تا ${setting.end_time} روز ${dayMapFa[response.day]}
                </span>
                <button class="btn btn-sm btn-light delete-schedule-setting" 
                        data-day="${setting.day}" 
                        data-start-time="${setting.start_time}" 
                        data-end-time="${setting.end_time}" data-day="${day}" data-selected-day="${setting.selected_day}">
                  <img src="${trashSvg}">
                </button>
              </div>
            `;
      }
     });
     settingsListHtml += '</div>';
     // اضافه کردن لیست تنظیمات به بدنه مدال
     $('#scheduleModal .modal-body').append(settingsListHtml);
    }
   },
   error: function() {
    toastr.error('خطا در دریافت تنظیمات');
   }
  });
 }
 // Function to calculate and update input values
 $(document).ready(function() {
  let morningStart, morningEnd; // متغیر برای ذخیره زمان شروع و پایان
  let totalMinutes; // متغیر برای ذخیره تعداد دقایق
  $(document).on("click", "[data-target='#CalculatorModal']", function() {
   const day = $(this).data("day");
   morningStart = $("#morning-start-" + day).val();
   morningEnd = $("#morning-end-" + day).val();
   $("#saveSelectionCalculator").data("day", day);
   // محاسبه تعداد دقایق
   if (morningStart && morningEnd) {
    const startTimeParts = morningStart.split(":");
    const endTimeParts = morningEnd.split(":");
    const startTimeHours = parseInt(startTimeParts[0]);
    const startTimeMinutes = parseInt(startTimeParts[1]);
    const endTimeHours = parseInt(endTimeParts[0]);
    const endTimeMinutes = parseInt(endTimeParts[1]);
    totalMinutes = (endTimeHours * 60 + endTimeMinutes) - (startTimeHours * 60 + startTimeMinutes);
    // مقداردهی اولیه به ورودی‌های مودال
   } else {
    totalMinutes = 0; // در صورت خالی بودن، تعداد دقایق را صفر قرار می‌دهیم
   }
  });
  // Event listener برای ورودی تعداد
  $(document).on("click", "input[name='appointment-count']", function() {
   $("#count-label-modal").prop("checked", true);
   $("#time-label-modal").prop("checked", false); // غیرفعال کردن چک باکس
  });
  $(document).on("input", "input[name='appointment-count']", function() {
   const countInput = $(this).val();
   const timePerAppointmentInput = $(this).closest('.modal-body').find("input[name='time-count']");
   // بررسی اینکه آیا کاربر عددی وارد کرده است
   if (countInput && !isNaN(countInput) && countInput > 0) {
    const timePerAppointment = totalMinutes / countInput; // محاسبه زمان برای هر نوبت
    timePerAppointmentInput.val(Math.round(timePerAppointment)); // قرار دادن مقدار در ورودی
    // فعال کردن چک باکس مربوطه
    $("#count-label-modal").prop("checked", true);
   } else {
    timePerAppointmentInput.val(""); // اگر ورودی عددی نیست، ورودی زمان را پاک کن
    $("#count-label-modal").prop("checked", false); // غیرفعال کردن چک باکس
   }
  });
  $(document).on("click", "input[name='time-count']", function() {
   $("#count-label-modal").prop("checked", false);
   $("#time-label-modal").prop("checked", true); // غیرفعال کردن چک باکس
  });
  // Event listener برای ورودی زمان هر نوبت
  $(document).on("input", "input[name='time-count']", function() {
   const timePerAppointmentInput = $(this).val();
   const countInput = $(this).closest('.modal-body').find("input[name='appointment-count']");
   // بررسی اینکه آیا کاربر عددی وارد کرده است
   if (timePerAppointmentInput && !isNaN(timePerAppointmentInput) && timePerAppointmentInput > 0) {
    const newCount = totalMinutes / timePerAppointmentInput; // محاسبه تعداد نوبت‌ها
    countInput.val(Math.round(newCount)); // قرار دادن مقدار در ورودی
    // فعال کردن چک باکس مربوطه
    $("#count-label-modal").prop("checked", false); // غیرفعال کردن چک باکس
    $("#time-label-modal").prop("checked", true);
   } else {
    countInput.val(""); // اگر ورودی عددی نیست، ورودی تعداد را پاک کن
    $("#time-label-modal").prop("checked", false); // غیرفعال کردن چک باکس
   }
  });
  $(document).on("click", "#saveSelectionCalculator", function() {
   const timePerAppointmentInput = $("input[name='time-count']").val();
   const countInput = $("input[name='appointment-count']").val();
   // دریافت روز خاصی که در آن هستیم
   const day = $(this).data("day"); // این را اضافه کنید تا روز خاص را بگیرید
   // انتخاب ورودی تعداد نوبت مربوط به آن روز خاص
   const countNobat = $("#morning-patients-" + day); // این را تغییر دهید
   if (timePerAppointmentInput && !isNaN(timePerAppointmentInput) && timePerAppointmentInput > 0) {
    const newCount = totalMinutes / timePerAppointmentInput; // محاسبه تعداد نوبت‌ها
    countNobat.val(Math.round(newCount)); // قرار دادن مقدار در ورودی
   }
   $("#CalculatorModal").modal("hide"); // بستن مدال
   $("#CalculatorModal").removeClass("show");
   $(".modal-backdrop").remove();
  });
  $(document).on('click', '#saveSchedule', function() {
   const $button = $(this);
   const $loader = $button.find('.loader');
   const $buttonText = $button.find('.button_text');
   const selected_day_choice_fa = $('.badge-time-styles-day.active-hover').text();
   const dayMap = {
    'شنبه': 'saturday',
    'یکشنبه': 'sunday',
    'دوشنبه': 'monday',
    'سه‌شنبه': 'tuesday',
    'چهارشنبه': 'wednesday',
    'پنج‌شنبه': 'thursday',
    'جمعه': 'friday'
   };
   const dayEn = dayMap[selected_day_choice_fa];
   // بررسی اینکه آیا برای این روز تنظیمات قبلی وجود دارد
   const existingSetting = $(`.setting-item[data-day="${dayEn}"]`);
   if (existingSetting.length > 0) {
    toastr.error(`شما از قبل برای ${selected_day_choice_fa} تنظیمات دارید. لطفاً ابتدا تنظیمات قبلی را حذف کنید.`);
    return;
   }
   $buttonText.hide();
   $loader.show();
   const scheduleStart = $('#schedule-start').val();
   const scheduleEnd = $('#schedule-end').val();
   const max_appointments = $("#scheduleModal").data('max-appointments');
   $('input[type="checkbox"][id$="-copy-modal"]:checked').each(function() {
    const day = $(this).attr('id').replace('-copy-modal', '');
   });
   if (!dayEn) {
    toastr.error('لطفاً حداقل یک روز را انتخاب کنید');
    $loader.hide();
    $buttonText.show();
    return;
   }
   $.ajax({
    url: "{{ route('save-appointment-settings') }}",
    method: 'POST',
    data: {
     start_time: scheduleStart,
     end_time: scheduleEnd,
     selected_days: dayEn,
     day: $('#scheduleModal').data('day'),
     max_appointments: max_appointments,
     _token: '{{ csrf_token() }}'
    },
    success: function(response) {
     toastr.success('تنظیمات با موفقیت ذخیره شد');
     $('.settings-list').remove();
     // به‌روزرسانی UI برای نمایش تنظیمات جدید
     updateSettingsUI(dayEn, scheduleStart, scheduleEnd);
     checkAllDaysSettings(dayEn, scheduleStart, scheduleEnd, max_appointments)
     $loader.hide();
     $buttonText.show();
    },
    error: function(xhr) {
     toastr.error(xhr.responseJSON.message || 'خطا در ذخیره‌سازی');
     $loader.hide();
     $buttonText.show();
    }
   });
  });
  // تابع برای به‌روزرسانی UI
 function updateSettingsUI(day, startTime, endTime) {
     const persianDay = getPersianDayName(day);
     const settingsHtml = `
        <div class="d-flex justify-content-between align-items-center border-bottom p-2 border-radius-4  mb-2 setting-item mt-3 bg-active-slot" data-day="${day}" data-selected-day="${day}">
            <span class="font-weight-bold text-success p-2">
                   باز شدن نوبت‌ها از ${startTime} تا ${endTime} روز ${persianDay}
            </span>
            <button class="btn btn-sm btn-light delete-schedule-setting" 
                    data-day="${day}" 
                    data-start-time="${startTime}" 
                    data-end-time="${endTime}" 
                    data-selected-day="${day}">
                <img src="${trashSvg}">
            </button>
        </div>
    `;
     $('#scheduleModal .modal-body').append(settingsHtml);
   }

  $(document).on('click', '.delete-schedule-setting', function() {
   const $settingItem = $(this).closest('.setting-item');
   const day = $("#saveSchedule").data('day');
   const startTime = $(this).data('start-time');
   const endTime = $(this).data('end-time');
   const selected_day = $(this).data('selected-day');
   Swal.fire({
    title: 'آیا مطمئن هستید؟',
    text: "این تنظیمات حذف خواهد شد!",
    icon: 'warning',
    showCancelButton: true,
    confirmButtonColor: '#3085d6',
    cancelButtonColor: '#d33',
    confirmButtonText: 'بله، حذف شود!',
    cancelButtonText: 'لغو'
   }).then((result) => {
    if (result.isConfirmed) {
     $.ajax({
      url: "{{ route('delete-schedule-setting') }}",
      method: 'POST',
      data: {
       day: day,
       selected_day: selected_day,
       start_time: startTime,
       end_time: endTime,
       _token: '{{ csrf_token() }}'
      },
      success: function(response) {
       // حذف ردیف تنظیمات
       $settingItem.remove();
       // بررسی اینکه آیا دیگر تنظیمی باقی مانده است
       if ($('.settings-list .setting-item').length === 0) {
        // حذف هشدار
        $('.settings-list').remove();
        $('#scheduleModal .modal-body .alert').remove();
        // فعال کردن فیلدهای مدال
        $('#schedule-start, #schedule-end').prop('disabled', false);
        $('#saveSchedule')
         .prop('disabled', false)
         .removeClass('btn-secondary')
         .addClass('btn-primary');
       }
       toastr.success('تنظیمات با موفقیت حذف شد');
      },
      error: function(xhr) {
       toastr.error('خطا در حذف تنظیمات');
      }
     });
    }
   });
  });
  function loadPreviousAppointmentSettings(day) {
   const start_time = $('[data-target="#scheduleModal"]').data('start-time')
   const end_time = $('[data-target="#scheduleModal"]').data('end-time')
   const max_appointments = $('[data-target="#scheduleModal"]').data('max-appointments')
   checkAllDaysSettings(day, start_time, end_time, max_appointments);
   $.ajax({
    url: "{{ route('get-appointment-settings') }}",
    method: 'GET',
    data: {
     day: day,
     start_time: start_time,
     end_time: end_time,
     max_appointments: max_appointments,
    },
    success: function(response) {
     if (response.status && response.settings) {
      $('#schedule-start').val(response.settings.start_time);
      $('#schedule-end').val(response.settings.end_time);
     }
    }
   });
  }
  $(document).ready(function() {
   // بررسی تنظیمات در زمان تغییر مقادیر
   $('#schedule-start, #schedule-end').on('change', function() {
    $('#saveSchedule').prop('disabled', false)
     .removeClass('btn-secondary')
     .addClass('btn-primary');
   });
  });
  // فراخوانی در زمان باز شدن مدال
  $(document).on('show.bs.modal', '#scheduleModal', function(event) {
   const $trigger = $(event.relatedTarget);
   const day = $trigger.data('day');
   loadPreviousAppointmentSettings(day);
  });
  // در زمان بارگذاری صفحه
 });
</script>
<div class="modal fade" id="scheduleModal" tabindex="-1" data-selected-day="" role="dialog"
 aria-labelledby="scheduleModalLabel" aria-hidden="true">
 <div class="modal-dialog modal-dialog-centered my-modal-lg" role="document">
  <div class="modal-content border-radius-8">
   <div class="modal-header">
    <h6 class="modal-title font-weight-bold" id="scheduleModalLabel">برنامه زمانبندی</h6>
    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
     <span aria-hidden="true">&times;</span>
    </button>
   </div>
   <div class="modal-body">
    <div class="">
     <div class="">
      <label class="font-weight-bold text-dark">روزهای کاری</label>
      <div class="mt-2 d-flex flex-wrap gap-10 justify-content-start my-768px-styles-day-and-times">
       <div class="" tabindex="0" role="button"><span class="badge-time-styles-day"
         data-day="saturday">شنبه</span><span class=""></span>
       </div>
       <div class="" tabindex="0" role="button"><span class="badge-time-styles-day"
         data-day="sunday">یکشنبه</span><span class=""></span>
       </div>
       <div class="" tabindex="0" role="button"><span class="badge-time-styles-day"
         data-day="monday">دوشنبه</span><span class=""></span>
       </div>
       <div class="" tabindex="0" role="button"><span class="badge-time-styles-day"
         data-day="tuesday">سه‌شنبه</span><span class=""></span>
       </div>
       <div class="" tabindex="0" role="button"><span class="badge-time-styles-day"
         data-day="wednesday">چهارشنبه</span><span class=""></span></div>
       <div class="" tabindex="0" role="button"><span class="badge-time-styles-day"
         data-day="thursday">پنج‌شنبه</span><span class=""></span></div>
       <div class="" tabindex="0" role="button"><span class="badge-time-styles-day"
         data-day="friday">جمعه</span><span class=""></span></div>
      </div>
     </div>
    </div>
    <div class="w-100 d-flex mt-4 gap-4 justify-content-center">
     <div class="form-group position-relative timepicker-ui">
      <label class="label-top-input-special-takhasos">شروع</label>
      <input type="text" class="form-control  h-50 timepicker-ui-input text-center font-weight-bold font-size-13"
       id="schedule-start" value="00:00">
     </div>
     <div class="form-group position-relative timepicker-ui">
      <label class="label-top-input-special-takhasos">پایان</label>
      <input type="text" class="form-control  h-50 timepicker-ui-input text-center font-weight-bold font-size-13"
       id="schedule-end" value="23:59">
     </div>
    </div>
    <div class="w-100 d-flex justify-content-end mt-3">
     <button type="submit" class="btn btn-primary h-50 col-12 d-flex justify-content-center align-items-center"
      id="saveSchedule">
      <span class="button_text">ذخیره تغیرات</span>
      <div class="loader"></div>
     </button>
    </div>
   </div>
  </div>
 </div>
</div>
<div class="modal fade" id="checkboxModal" tabindex="-1" role="dialog" aria-labelledby="checkboxModalLabel"
 aria-hidden="true">
 <div class="modal-dialog modal-dialog-centered" role="document">
  <div class="modal-content border-radius-8">
   <div class="modal-header">
    <h6 class="modal-title font-weight-bold" id="checkboxModalLabel"> کپی ساعت کاری برای روز های : </h6>
    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
     <span aria-hidden="true">&times;</span>
    </button>
   </div>
   <div class="modal-body">
    <div class="">
     <div class="d-flex flex-wrap flex-column lh-2 align-items-start gap-4">
      <x-my-check :isChecked="false" id="select-all-copy-modal" day="انتخاب همه" />
      <x-my-check :isChecked="false" id="saturday-copy-modal" day="شنبه" />
      <x-my-check :isChecked="false" id="sunday-copy-modal" day="یکشنبه" />
      <x-my-check :isChecked="false" id="monday-copy-modal" day="دوشنبه" />
      <x-my-check :isChecked="false" id="tuesday-copy-modal" day="سه‌شنبه" />
      <x-my-check :isChecked="false" id="wednesday-copy-modal" day="چهارشنبه" />
      <x-my-check :isChecked="false" id="thursday-copy-modal" day="پنج‌شنبه" />
      <x-my-check :isChecked="false" id="friday-copy-modal" day="جمعه" />
     </div>
    </div>
   </div>
   <div class="w-100 d-flex justify-content-between p-3 gap-4">
    <button type="submit" class="btn btn-primary h-50 col-8 d-flex justify-content-center align-items-center w-100"
     id="saveSelection">
     <span class="button_text">ذخیره تغیرات</span>
     <div class="loader"></div>
    </button>
    <button type="button" class="btn btn-danger h-50 w-50 col-4" data-dismiss="modal">لغو</button>
   </div>
  </div>
 </div>
</div>
<div class="modal fade" id="CalculatorModal" tabindex="-1" role="dialog" aria-labelledby="CalculatorModalLabel"
 aria-hidden="true">
 <div class="modal-dialog modal-dialog-centered" role="document">
  <div class="modal-content border-radius-8" id="calculate-modal">
   <div class="modal-header">
    <h6 class="modal-title font-weight-bold" id="checkboxModalLabel"> انتخاب تعداد نوبت یا زمان ویزیت: </h6>
    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
     <span aria-hidden="true">&times;</span>
    </button>
   </div>
   <div class="modal-body">
    <div class="d-flex align-items-center">
     <div class="d-flex flex-wrap flex-column  align-items-start gap-4">
      <div class="d-flex align-items-center">
       <x-my-check :isChecked="false" id="count-label-modal" day="" />
       <div class="input-group position-relative mx-2">
        <label class="label-top-input-special-takhasos">نوبت ها </label>
        <input type="text" value="{{ old('appointment-count') }}"
         class="form-control   text-center h-50 border-radius-0" name="appointment-count">
        <div class="input-group-append count-span-prepand-style"><span class="input-group-text px-2">نوبت</span></div>
       </div>
      </div>
      <div class="d-flex align-items-center mt-4">
       <x-my-check :isChecked="false" id="time-label-modal" day="" />
       <div class="input-group position-relative mx-2">
        <label class="label-top-input-special-takhasos"> هر نوبت </label>
        <input type="text" value="{{ old('time-count') }}"
         class="form-control   text-center h-50 border-radius-0" name="time-count">
        <div class="input-group-append"><span class="input-group-text px-2">دقیقه</span></div>
       </div>
      </div>
     </div>
    </div>
    <div class="w-100 d-flex justify-content-end p-1 gap-4 mt-3">
     <button type="submit" class="btn btn-primary h-50 col-12 d-flex justify-content-center align-items-center"
      id="saveSelectionCalculator">
      <span class="button_text">ذخیره تغیرات</span>
      <div class="loader"></div>
     </button>
    </div>
   </div>
  </div>
 </div>
</div>
