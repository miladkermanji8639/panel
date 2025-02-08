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
  // استخراج ساعات کاری
  let workHours = [];
  workHours = schedule.work_hours ? JSON.parse(schedule.work_hours) : []; // تبدیل JSON به آرایه
  // اگر ساعات کاری وجود ندارد، المان اصلی به همراه دکمه "افزودن ردیف جدید" نمایش داده شود
  if (!workHours || workHours.length === 0) {
   const mainRowHtml = `
      <div class="mt-3 form-row d-flex justify-content-between w-100 p-3 bg-active-slot border-radius-4" data-slot-id="">
        <div class="d-flex justify-content-start align-items-center gap-4">
          <div class="form-group position-relative timepicker-ui">
            <label class="label-top-input-special-takhasos">از</label>
            <input type="text" class="form-control h-50 timepicker-ui-input text-center font-weight-bold font-size-13 bg-white" id="morning-start-${day}" value="">
          </div>
          <div class="form-group position-relative timepicker-ui">
            <label class="label-top-input-special-takhasos">تا</label>
            <input type="text" class="form-control h-50 timepicker-ui-input text-center font-weight-bold font-size-13 bg-white" id="morning-end-${day}" value="">
          </div>
          <div class="form-group col-sm-3 position-relative">
            <label class="label-top-input-special-takhasos">تعداد نوبت</label>
            <input type="text" class="form-control h-50 text-center max-appointments bg-white" name="nobat-count" min="0" id="morning-patients-${day}" data-toggle="modal" data-target="#CalculatorModal" data-day="${day}" data-start-time="" data-end-time="" value="" readonly>
          </div>
          <div class="form-group col-sm-1 position-relative">
            <button class="btn btn-light btn-sm copy-single-slot-btn" data-toggle="modal" data-target="#checkboxModal" data-day="${day}" data-start-time="" data-end-time="" data-max-appointments="" data-slot-id="" disabled>
              <img src="${svgUrl}">
            </button>
          </div>
          <div class="form-group col-sm-2 position-relative">
            <button class="btn btn-light btn-sm remove-row-btn" disabled data-day="${day}" data-start-time="" data-end-time="" data-max-appointments="" data-slot-id="">
              <img src="${trashSvg}">
            </button>
          </div>
        </div>
        <div class="d-flex align-items-center">
          <div class="d-flex align-items-center">
            <button type="button" class="btn text-black btn-sm btn-outline-primary schedule-btn" data-toggle="modal" data-target="#scheduleModal" data-day="${day}" disabled>زمانبندی باز شدن نوبت‌ها</button>
          </div>
        </div>
      </div>
      <div class="add-new-row mt-3">
        <button class="add-row-btn btn btn-sm btn-primary" data-day="${day}">
          <span>+</span>
          <span>افزودن ردیف جدید</span>
        </button>
      </div>
    `;
   $container.append(mainRowHtml);
  } else {
   // اگر ساعات کاری وجود دارد، آنها را به همراه دکمه "افزودن ردیف جدید" اضافه کن
   workHours.forEach(slot => {
    const slotHtml = createSlotHtml(schedule, day);
    $container.append(slotHtml);
   });
   // افزودن دکمه "افزودن ردیف جدید" به انتهای لیست
   const addNewRowHtml = `
      <div class="add-new-row mt-3">
        <button class="add-row-btn btn btn-sm btn-primary" data-day="${day}">
          <span>+</span>
          <span>افزودن ردیف جدید</span>
        </button>
      </div>
    `;
   $container.append(addNewRowHtml);
  }
  // بازسازی تایم‌پیکرها
  initializeTimepicker();
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
  const start_time = slot?.time_slots?.start_time || "";
  const end_time = slot?.time_slots?.end_time || "";
  const max_appointments = slot?.max_appointments || '';
  const day = slot?.day || "sunday"; // مقدار پیش‌فرض
  const slotId = slot?.id || "";
  // تولید HTML با ورودی‌های تابع
  return `
    <div class="mt-3 form-row d-flex justify-content-between w-100 p-3 bg-active-slot border-radius-4" data-slot-id="${slotId || ''}">
      <div class="d-flex justify-content-start align-items-center gap-4">
        <div class="form-group position-relative timepicker-ui">
          <label class="label-top-input-special-takhasos">از</label>
          <input type="text" class="form-control h-50 timepicker-ui-input text-center font-weight-bold font-size-13 start-time bg-white" value="${start_time}" readonly ${start_time ? 'disabled' : ''}>
        </div>
        <div class="form-group position-relative timepicker-ui">
          <label class="label-top-input-special-takhasos">تا</label>
          <input type="text" class="form-control h-50 timepicker-ui-input text-center font-weight-bold font-size-13 end-time bg-white" value="${end_time}" readonly ${end_time ? 'disabled' : ''}>
        </div>
        <div class="form-group col-sm-3 position-relative">
          <label class="label-top-input-special-takhasos">تعداد نوبت</label>
          <input type="text" class="form-control h-50 text-center max-appointments bg-white"  name="nobat-count" min="0" id="morning-patients-${day}"  data-toggle="modal" data-target="#CalculatorModal" data-day="${day}" data-start-time="" data-end-time="" value="" readonly ${slot ? 'disabled' : ''}>
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
        <button type="button" class="btn btn-outline-primary btn-sm schedule-btn" 
          data-toggle="modal" 
          data-target="#scheduleModal" 
          data-day="${day}" data-start-time="${start_time}" data-end-time="${end_time}" data-max-appointments="${max_appointments}">
          زمانبندی باز شدن نوبت‌ها
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

 function loadWorkSchedule(response) {
  try {
   response.workSchedules.forEach(function(schedule) {
    
    $(`#${schedule.day}`).prop('checked', schedule.is_working);
    if (schedule.is_working) {
     $(`.work-hours-${schedule.day}`).removeClass('d-none');
    } else {
     $(`.work-hours-${schedule.day}`).addClass('d-none');
    }
    updateDayUI(schedule);
   });
  } catch (error) {
   console.error("Error in loadWorkSchedule:", error);
  }
 }

 function createSlotHtml(slot, day) {
  let workHours = slot.work_hours ? JSON.parse(slot.work_hours) : [];
  let slotHtml = "";
  // پاک کردن محتوای قبلی
  const $container = $(`#morning-${day}-details`);
  $container.empty(); // این خط باعث جلوگیری از تکرار ردیف‌ها می‌شود
  workHours.forEach((timeSlot) => {
   const startTime = timeSlot.start ?? '';
   const endTime = timeSlot.end ?? '';
   const maxAppointments = timeSlot.max_appointments || '';
  
   
   slotHtml += `
            <div class="mt-3 form-row d-flex justify-content-between w-100 p-3 bg-active-slot border-radius-4" data-slot-id="${slot.id}">
                <div class="d-flex justify-content-start align-items-center gap-4">
                    <div class="form-group position-relative timepicker-ui">
                        <label class="label-top-input-special-takhasos">از</label>
                        <input type="text" class="form-control h-50 timepicker-ui-input text-center font-weight-bold font-size-13 start-time bg-white" value="${startTime}" readonly ${startTime ? 'disabled' : ''}>
                    </div>
                    <div class="form-group position-relative timepicker-ui">
                        <label class="label-top-input-special-takhasos">تا</label>
                        <input type="text" class="form-control h-50 timepicker-ui-input text-center font-weight-bold font-size-13 end-time bg-white" value="${endTime}" readonly ${endTime? 'disabled' : ''}>
                    </div>
                    <div class="form-group col-sm-3 position-relative">
                        <label class="label-top-input-special-takhasos">تعداد نوبت</label>
                        <input type="text" name="nobat-count" id="morning-patients-${day}" class="form-control h-50 text-center max-appointments bg-white" value="${maxAppointments}" data-toggle="modal" data-target="#CalculatorModal" data-start-time="" data-end-time=""readonly ${maxAppointments ? 'disabled' : ''}>
                    </div>
                    <div class="form-group col-sm-1 position-relative">
                        <button class="btn btn-light btn-sm copy-single-slot-btn" 
                            data-toggle="modal" data-target="#checkboxModal" 
                            data-day="${day}" 
                            data-start-time="${startTime}" 
                            data-end-time="${endTime}" 
                            data-max-appointments="${maxAppointments}" 
                            data-slot-id="${slot.id}">
                            <img src="${svgUrl}">
                        </button>
                    </div>
                    <div class="form-group col-sm-2 position-relative">
                        <button class="btn btn-light btn-sm remove-row-btn" 
                            data-slot-id="${slot.id}" 
                            data-start-time="${startTime}" 
                            data-end-time="${endTime}" 
                            data-max-appointments="${maxAppointments}" 
                            data-day="${day}">
                            <img src="${trashSvg}">
                        </button>
                    </div>
                </div>
                <div class="d-flex align-items-center">
                    <button type="button" class="btn btn-outline-primary btn-sm schedule-btn" 
                        data-toggle="modal" 
                        data-target="#scheduleModal" 
                        data-day="${day}" 
                        data-start-time="${startTime}" 
                        data-end-time="${endTime}" 
                        data-max-appointments="${maxAppointments}">
                        زمانبندی باز شدن نوبت‌ها
                    </button>
                </div>
            </div>
        `;
  });
  // اضافه کردن HTML به DOM
  $container.append(slotHtml);
 }
 // استفاده از کش
 $(document).ready(function() {
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
 $(document).on('click', '[data-target="#scheduleModal"]', function() {
  $("#saveSchedule").removeData('workhours');
  const day = $(this).data('day');
  const start_time = $(this).data('start-time')
  const end_time = $(this).data('end-time')
  const max_appointments = $(this).data('max-appointments')

  $('#scheduleModal').data('currentDay', day); // ذخیره روز جاری در مدال
  $("#saveSchedule").attr('data-day', day);
  $("#saveSchedule").attr('data-workhours', `${day}-${start_time}-${end_time}-${max_appointments}`);
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
  const currentWorkHours = $("#saveSchedule").data('workhours')
  $.ajax({
   url: "{{ route('get-appointment-settings') }}",
   method: 'GET',
   data: {
    day: day,
    start_time: start_time,
    end_time: end_time,
    max_appointments: max_appointments,
    currentWorkHours: currentWorkHours,
   },
   success: function(response) {
    console.log(response);
    
    
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
 });
 // تابع تبدیل نام روز به فارسی (اگر قبلاً تعریف نشده باشد)
 function addNewRow(day) {
  const newRow = `
        <div class="mt-3 form-row d-flex justify-content-between w-100 p-3 bg-active-slot border-radius-4" data-slot-id="">
            <div class="d-flex justify-content-start align-items-center gap-4">
                <div class="form-group position-relative timepicker-ui">
                    <label class="label-top-input-special-takhasos">از</label>
                    <input type="text" class="form-control h-50 timepicker-ui-input text-center font-weight-bold font-size-13 bg-white" id="morning-start-${day}" value="">
                </div>
                <div class="form-group position-relative timepicker-ui">
                    <label class="label-top-input-special-takhasos">تا</label>
                    <input type="text" class="form-control h-50 timepicker-ui-input text-center font-weight-bold font-size-13 bg-white" id="morning-end-${day}" value="">
                </div>
                <div class="form-group col-sm-3 position-relative">
                    <label class="label-top-input-special-takhasos">تعداد نوبت</label>
                    <input type="text" class="form-control h-50 text-center max-appointments bg-white"  name="nobat-count" min="0" id="morning-patients-${day}"  data-toggle="modal" data-target="#CalculatorModal" data-day="${day}" data-start-time="" data-end-time="" value="" readonly>
                </div>
                 <div class="form-group col-sm-1 position-relative">
            <button class="btn btn-light btn-sm copy-single-slot-btn" data-toggle="modal" data-target="#checkboxModal" data-day="${day}" data-start-time="" data-end-time="" data-max-appointments="" data-slot-id="">
                <img src="${svgUrl}">
            </button>
          </div>
                <div class="form-group col-sm-2 position-relative">
                    <button class="btn btn-light btn-sm remove-row-btn" data-day="${day}" data-start-time="" data-end-time="" data-max-appointments="" data-slot-id="">
                        <img src="${trashSvg}">
                    </button>
                </div>
            </div>
        </div>
    `;
  const $container = $(`#morning-${day}-details`);
  $container.append(newRow);
 }
 $(document).on("click", ".remove-row-btn", function() {
  let $row = $(this).closest(".form-row"); // پیدا کردن ردیف مربوطه
  let $container = $row.closest('[id^="morning-"]'); // پیدا کردن کانتینر روز مربوطه
  let slotId = $(this).data('slot-id');
  let day = $(this).data('day');
  let startTime = $(this).data('start-time');
  let endTime = $(this).data('end-time');

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
      day: day,
      start_time: startTime,
      end_time: endTime,
     },
     success: function(response) {
      let totalRows = $container.find(".form-row").length;

      if (totalRows === 1) {
       //  اگر فقط یک ردیف باقی مانده بود:
       $row.find("input").val("").prop("disabled", false);
       $row.find(".remove-row-btn, .copy-single-slot-btn, .schedule-btn").prop("disabled", true);
       $row.attr("data-slot-id", ""); // پاک کردن slot-id

       //  مقداردهی مجدد `data-day` برای جلوگیری از خطا
       let maxAppointmentsInput = $row.find(".max-appointments");
       maxAppointmentsInput.attr("data-day", day).data("day", day);
      } else {
       //  اگر بیش از یک ردیف بود، فقط ردیف را حذف کن
       $row.remove();
      }

      toastr.success('حذف موفقیت‌آمیز');

      //  دوباره بارگذاری ساعات کاری و مقداردهی `data-day`
      initializeTimepicker();
     },
     error: function(xhr) {
      toastr.error('خطا در حذف');
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
      work_hours: workHours.length > 0 ? JSON.stringify(workHours) : nullh
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

 function checkRowInputs($row, day) {
  let $startTimeInput = $row.find(`#morning-start-${day}`);
  let $endTimeInput = $row.find(`#morning-end-${day}`);
  let $maxAppointmentsInput = $row.find(`#morning-patients-${day}`);
  // بررسی وجود اینپوت‌ها قبل از دسترسی به مقدارشان
  let startTime = $startTimeInput.length ? $startTimeInput.val().trim() : '';
  let endTime = $endTimeInput.length ? $endTimeInput.val().trim() : '';
  let maxAppointments = $maxAppointmentsInput.length ? $maxAppointmentsInput.val().trim() : '';
  let isValid = startTime.length > 0 && endTime.length > 0 && maxAppointments.length > 0 &&
   !isNaN(maxAppointments) && parseInt(maxAppointments) > 0;
  $row.find(".remove-row-btn, .copy-single-slot-btn, .schedule-btn").prop("disabled", !isValid);
 }

 function loadAllWorkhours() {
  $.ajax({
   url: "{{ route('dr-get-work-schedule') }}",
   method: 'GET',
   success: function(response) {
    $.each(response.workSchedules, function(index, schedule) {
      
     let day = schedule.day;
     let hasData = false;
     if (schedule.work_hours) {
      let workHours = JSON.parse(schedule.work_hours);
      $.each(workHours, function(i, slot) {
       let startTime = slot.start || "";
       let endTime = slot.end || "";
       let maxAppointments = slot.max_appointments || "";
       let slotId = schedule.id || "";
       if (startTime) {
        $(`#morning-start-${day}`).val(startTime).prop("disabled", true);
       }
       if (endTime) {
        $(`#morning-end-${day}`).val(endTime).prop("disabled", true);
       }
       if (maxAppointments) {
        $(`#morning-patients-${day}`).val(maxAppointments).prop("disabled", true);
       }

       $(`#morning-${day}-details .remove-row-btn`).attr({
        "data-slot-id": slotId,
        "data-start-time": startTime,
        "data-end-time": endTime,
        "data-max-appointments": maxAppointments,
        "data-day": day
       }).prop("disabled", false);
       $(`#morning-${day}-details .copy-single-slot-btn`).attr({
        "data-day": day,
        "data-start-time": startTime,
        "data-end-time": endTime,
        "data-max-appointments": maxAppointments
       }).prop("disabled", false);
       $(`[data-target="#scheduleModal"][data-day="${day}"]`).prop("disabled", false);
       hasData = true;
      });
     }
    });
   },
   error: function(xhr) {
    console.error("خطا در دریافت داده‌های ساعات کاری:", xhr.responseText);
   }
  });
 }

 function timeToMinutes(time) {
  let [hours, minutes] = time.split(':').map(Number);
  return hours * 60 + minutes;
 }
 $(document).ready(function() {
  $(document).on("click", ".timepicker-ui-ok-btn", function() {
   let $timepicker = $(this).closest(".timepicker-ui-modal");
   let hour = $timepicker.find(".timepicker-ui-hour").val();
   let minute = $timepicker.find(".timepicker-ui-minutes").val();

   if (hour !== "" && minute !== "") {
    let selectedTime = `${hour.padStart(2, '0')}:${minute.padStart(2, '0')}`;
    let $targetInput = $(".timepicker-ui-input.active");

    if ($targetInput.length) {
     let row = $targetInput.closest(".form-row");
     let maxAppointmentsInput = row.find(".max-appointments");

     // مقداردهی به `start-time` یا `end-time`
     let inputId = $targetInput.attr("id") || "";

     if (inputId.includes("start")) {
      maxAppointmentsInput.attr("data-start-time", selectedTime).data("start-time", selectedTime);
     } else if (inputId.includes("end")) {
      maxAppointmentsInput.attr("data-end-time", selectedTime).data("end-time", selectedTime);
     }

     // مقداردهی به `value` اینپوت برای بررسی تغییرات
     $targetInput.val(selectedTime).attr("value", selectedTime);

     // مقداردهی `data-day` از `maxAppointmentsInput`
     let day = maxAppointmentsInput.data("day");
     $targetInput.attr("data-day", day);

     // بروزرسانی مقدار در DOM
     $targetInput.trigger("change");
     maxAppointmentsInput.trigger("change");
    }
   }

   // بستن تایم‌پیکر
   $timepicker.removeClass("show");
  });
  $(document).on("change", ".timepicker-ui-input", function() {
   let row = $(this).closest(".form-row");
   let maxAppointmentsInput = row.find(".max-appointments");
   let day = maxAppointmentsInput.data("day");

   let startTime = row.find(".start-time").val();
   let endTime = row.find(".end-time").val();

   maxAppointmentsInput.attr("data-start-time", startTime).data("start-time", startTime);
   maxAppointmentsInput.attr("data-end-time", endTime).data("end-time", endTime);

   maxAppointmentsInput.trigger("change");
  });


  // تابع تبدیل زمان به دقیقه برای مقایسه
  function timeToMinutes(time) {
   if (!time || typeof time !== "string") return null;
   let [hours, minutes] = time.split(':').map(Number);
   return (isNaN(hours) || isNaN(minutes)) ? null : hours * 60 + minutes;
  }
  // تنظیم کلاس 'active' روی اینپوتی که تایم پیکر برای آن باز شده
  $(document).on("click", ".timepicker-ui-input", function() {
   $(".timepicker-ui-input").removeClass("active");
   $(this).addClass("active");
  });

  // ذخیره مقدار هنگام تغییر تعداد نوبت‌ها
  $(document).on("click", ".add-row-btn", function() {
   let day = $(this).data("day");
   let $container = $(`#morning-${day}-details`);
   let $addButton = $container.find(".add-new-row");
   let hasIncompleteRow = false;
   // بررسی اینکه آیا ردیف‌های قبلی مقدار دارند یا نه
   $container.find(".form-row").each(function() {
    let $row = $(this);
    let startTime = $row.find("input.start-time").val()?.trim() || "";
    let endTime = $row.find("input.end-time").val()?.trim() || "";
    let maxAppointments = $row.find("input.max-appointments").val()?.trim() || "";
    let slotId = $row.attr('data-slot-id') || '';
    // بررسی تکمیل بودن مقادیر برای جلوگیری از اضافه کردن ردیف جدید
    if (slotId === "") {
     hasIncompleteRow = true;
     return false; // خروج از حلقه
    }
   });
   if (hasIncompleteRow) {
    toastr.error("⚠ لطفاً ابتدا ردیف قبلی را تکمیل کنید.");
    return; // متوقف کردن افزودن ردیف جدید
   }
   let newRow = $(`
        <div class="mt-3 form-row d-flex justify-content-between w-100 p-3 bg-active-slot border-radius-4" data-slot-id="">
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
                    <input type="text" class="form-control h-50 text-center max-appointments bg-white" data-day="${day}" name="nobat-count" id="morning-patients-${day}" data-start-time="" data-end-time="" value="" data-toggle="modal" data-target="#CalculatorModal" readonly>
                </div>
                <div class="form-group col-sm-1 position-relative">
                    <button class="btn btn-light btn-sm copy-single-slot-btn" data-toggle="modal" data-target="#checkboxModal" data-day="${day}">
                        <img src="${svgUrl}">
                    </button>
                </div>
                <div class="form-group col-sm-2 position-relative">
                    <button class="btn btn-light btn-sm remove-row-btn" data-day="${day}">
                        <img src="${trashSvg}">
                    </button>
                </div>
            </div>
            <div class="d-flex align-items-center">
                <button type="button" class="btn text-black btn-sm btn-outline-primary schedule-btn" data-toggle="modal" data-start-time="" data-end-time="" data-max-appointments="" data-target="#scheduleModal" data-day="${day}">زمانبندی باز شدن نوبت‌ها</button>
            </div>
        </div>
    `);
   // اضافه کردن قبل از دکمه "افزودن ردیف جدید"
   newRow.insertBefore($addButton);
   // تنظیم تایم‌پیکر روی ورودی‌های جدید
   initializeTimepicker();
   checkRowInputs(newRow, day);
  });
 });
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
        <div class="border-333 p-3 mt-3 border-radius-4">
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
            <div class="mt-3 form-row d-flex justify-content-between w-100 p-3 bg-active-slot border-radius-4" data-slot-id="">
        <div class="d-flex justify-content-start align-items-center gap-4">
          <div class="form-group position-relative timepicker-ui">
            <label class="label-top-input-special-takhasos">از</label>
            <input type="text" class="form-control h-50 timepicker-ui-input text-center font-weight-bold font-size-13 bg-white" id="morning-start-${day}" value="">
          </div>
          <div class="form-group position-relative timepicker-ui">
            <label class="label-top-input-special-takhasos">تا</label>
            <input type="text" class="form-control h-50 timepicker-ui-input text-center font-weight-bold font-size-13 bg-white" id="morning-end-${day}" value="">
          </div>
          <div class="form-group col-sm-3 position-relative">
            <label class="label-top-input-special-takhasos">تعداد نوبت</label>
           <input type="text" class="form-control h-50 text-center max-appointments bg-white"  name="nobat-count" min="0" id="morning-patients-${day}"  data-toggle="modal" data-target="#CalculatorModal" data-day="${day}" data-start-time="" data-end-time="" value="" readonly>
          </div>
           <div class="form-group col-sm-1 position-relative">
            <button class="btn btn-light btn-sm copy-single-slot-btn" data-toggle="modal" data-target="#checkboxModal" data-day="${day}" data-start-time="" data-end-time="" data-max-appointments="" data-slot-id="">
                <img src="${svgUrl}">
            </button>
          </div>
          <div class="form-group col-sm-2 position-relative">
            <button class="btn btn-light btn-sm remove-row-btn" data-day="${day}" data-start-time="" data-end-time="" data-max-appointments="" data-slot-id="">
              <img src="${trashSvg}">
            </button>
          </div>
        </div>
        <div class="d-flex align-items-center">
          <div class="d-flex align-items-center">
              <button type="button" class="btn text-black  btn-sm btn-outline-primary schedule-btn" data-toggle="modal" data-target="#scheduleModal" data-day="${day}" data-start-time="" data-end-time="" data-max-appointments="" data-slot-id="">زمانبندی باز شدن نوبت‌ها</button>
          </div>
        </div>
      </div>
       <div class="add-new-row mt-3">
        <button class="add-row-btn btn btn-sm btn-primary" data-day="${day}">
          <span>
            +
          </span>
          <span>افزودن ردیف جدید</span>
        </button>
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
      <div class="mt-3 form-row d-flex justify-content-between w-100 p-3 bg-active-slot border-radius-4" data-slot-id="">
        <div class="d-flex justify-content-start align-items-center gap-4">
          <div class="form-group position-relative timepicker-ui">
            <label class="label-top-input-special-takhasos">از</label>
            <input type="text" class="form-control h-50 timepicker-ui-input text-center font-weight-bold font-size-13 bg-white" id="morning-start-${day}" value="">
          </div>
          <div class="form-group position-relative timepicker-ui">
            <label class="label-top-input-special-takhasos">تا</label>
            <input type="text" class="form-control h-50 timepicker-ui-input text-center font-weight-bold font-size-13 bg-white" id="morning-end-${day}" value="">
          </div>
          <div class="form-group col-sm-3 position-relative">
            <label class="label-top-input-special-takhasos">تعداد نوبت</label>
           <input type="text" class="form-control h-50 text-center max-appointments bg-white"  name="nobat-count" min="0" id="morning-patients-${day}"  data-toggle="modal" data-target="#CalculatorModal" data-day="${day}" data-start-time="" data-end-time="" value="" readonly>
          </div>
           <div class="form-group col-sm-1 position-relative">
            <button class="btn btn-light btn-sm copy-single-slot-btn" data-toggle="modal" data-target="#checkboxModal" data-day="${day}" data-start-time="" data-end-time="" data-max-appointments="" data-slot-id="">
                <img src="${svgUrl}">
            </button>
          </div>
          <div class="form-group col-sm-2 position-relative">
            <button class="btn btn-light btn-sm remove-row-btn" data-day="${day}" data-start-time="" data-end-time="" data-max-appointments="" data-slot-id="">
              <img src="${trashSvg}">
            </button>
          </div>
        </div>
        <div class="d-flex align-items-center">
          <div class="d-flex align-items-center">
              <button type="button" class="btn text-black  btn-sm btn-outline-primary schedule-btn" data-toggle="modal" data-target="#scheduleModal" data-day="${day}" data-start-time="" data-end-time="" data-max-appointments="" data-slot-id="">زمانبندی باز شدن نوبت‌ها</button>
          </div>
        </div>
      </div>
        <div class="add-new-row mt-3">
        <button class="add-row-btn btn btn-sm btn-primary" data-day="${day}">
          <span>
            +
          </span>
          <span>افزودن ردیف جدید</span>
        </button>
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
  // Event listeners for adding and removing rows
  $.each(days, function(index, day) {
   $("#" + day).on("change", function() {
    let day = $(this).attr("id");
    if ($(this).is(":checked")) {
     $(".work-hours-" + day).removeClass("d-none");
     // بررسی اینکه آیا روز موردنظر نوبت دارد یا نه
     setTimeout(() => {}, 500); // کمی تاخیر برای اطمینان از لود شدن داده‌ها
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
   let currentRow = $(this).closest(".form-row"); // دریافت ردیف جاری
   // دریافت مقدار `data-start-time` و `data-end-time` از اینپوت `max-appointments` در همان ردیف
   morningStart = currentRow.find(".max-appointments").data("start-time");
   morningEnd = currentRow.find(".max-appointments").data("end-time");
   $("#CalculatorModal").data("currentRow", currentRow);
   // محاسبه تعداد دقایق
   if (morningStart && morningEnd) {
    const startTimeParts = morningStart.split(":");
    const endTimeParts = morningEnd.split(":");
    const startTimeHours = parseInt(startTimeParts[0]);
    const startTimeMinutes = parseInt(startTimeParts[1]);
    const endTimeHours = parseInt(endTimeParts[0]);
    const endTimeMinutes = parseInt(endTimeParts[1]);
    totalMinutes = (endTimeHours * 60 + endTimeMinutes) - (startTimeHours * 60 + startTimeMinutes);
   } else {
    totalMinutes = 0; // در صورت خالی بودن مقدار، تعداد دقایق را صفر قرار می‌دهیم
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
   if ($(this).data("clicked")) return; //  جلوگیری از اجرای مکرر
   $(this).data("clicked", true);

   let currentRow = $("#CalculatorModal").data("currentRow"); // دریافت ردیف جاری
   let newValue = $("input[name='appointment-count']").val(); // مقدار جدید تعداد نوبت‌ها

   if (!newValue || isNaN(newValue) || parseInt(newValue) <= 0) {
    $(this).data("clicked", false); // بازنشانی مقدار
    toastr.warning('⚠ لطفاً مقدار معتبر وارد کنید.');
    return;
   }

   if (!currentRow || !currentRow.length) {
    toastr.error("خطا در ثبت مقدار، لطفاً دوباره امتحان کنید.");
    return;
   }

   let startTime = currentRow.find(".max-appointments").attr("data-start-time") || null;
   let endTime = currentRow.find(".max-appointments").attr("data-end-time") || null;
   let day = currentRow.find(".max-appointments").data('day');

   if (!startTime || !endTime) {
    toastr.error("زمان شروع و پایان مشخص نشده است.");
    return;
   }

   // ارسال داده‌ها به سرور
   $.ajax({
    url: "{{ route('save-time-slot') }}",
    method: "POST",
    data: {
     day: day,
     start_time: startTime,
     end_time: endTime,
     max_appointments: parseInt(newValue),
     _token: $('meta[name="csrf-token"]').attr('content')
    },
   success: function (response) {
       if (response.status) {
         toastr.success('✅ ساعت کاری با موفقیت اضافه شد');
         currentRow.find(".remove-row-btn, .copy-single-slot-btn, .schedule-btn").prop("disabled", false);

         // مقدار جدید را داخل input تعداد نوبت قرار بده
         currentRow.find(".max-appointments")
           .val(newValue)
           .attr("data-max-appointments", newValue)
           .prop("disabled", true) // بعد از ثبت موفق، غیر‌فعال شود
           .trigger("change");

         // غیرفعال‌سازی فقط اینپوت‌های زمان، دکمه‌ها فعال می‌مانند!
         currentRow.find(".start-time").val(startTime).prop("disabled", true);
         currentRow.find(".end-time").val(endTime).prop("disabled", true);
         currentRow.find(".max-appointments").val(newValue).prop("disabled", true);

         // خواندن مقدار `work_hours` از `response.workSchedule`
         let workHours = response.workSchedule.work_hours ? JSON.parse(response.workSchedule.work_hours) : [];
         let lastWorkHour = workHours.length ? workHours[workHours.length - 1] : null;
         
         // مقداردهی با استفاده از شرط یک‌خطی (ternary operator)
         let finalStartTime = startTime || (lastWorkHour ? lastWorkHour.start : "");
         let finalEndTime = endTime || (lastWorkHour ? lastWorkHour.end : "");
         let finalMaxAppointments = newValue || (lastWorkHour ? lastWorkHour.max_appointments : "");
        
         // ذخیره `slot_id` در ردیف جاری
         if (response.workSchedule.id) {
           currentRow.attr("data-slot-id", response.workSchedule.id);
           currentRow.find('.schedule-btn').attr({
             "data-start-time": finalStartTime,
             "data-end-time": finalEndTime,
             "data-max-appointments": parseInt(finalMaxAppointments),
             "data-slot-id": response.workSchedule.id
           });
            currentRow.find('.remove-row-btn').attr({
             "data-start-time": finalStartTime,
             "data-end-time": finalEndTime,
             "data-max-appointments": parseInt(finalMaxAppointments),
             "data-slot-id": response.workSchedule.id
           });
           currentRow.find('.remove-row-btn').attr("data-slot-id", response.workSchedule.id);
           currentRow.find('.copy-single-slot-btn').attr("data-slot-id", response.workSchedule.id);
         }
       }
     },


    error: function(xhr) {
     toastr.error(xhr.responseJSON?.message || 'خطا در ذخیره اطلاعات.');
    },
    complete: function() {
     $("#saveSelectionCalculator").data("clicked", false); //  بازنشانی مقدار
    }
   });

   // بستن مدال
   $("#CalculatorModal").modal("hide");
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
   const workhours_identifier = $(this).data('workhours')
   
   $.ajax({
    url: "{{ route('save-appointment-settings') }}",
    method: 'POST',
    data: {
     start_time: scheduleStart,
     end_time: scheduleEnd,
     selected_days: dayEn,
     workhours_identifier: workhours_identifier,
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
  $(document).ready(function() {
   // بررسی تنظیمات در زمان تغییر مقادیر
   $('#schedule-start, #schedule-end').on('change', function() {
    $('#saveSchedule').prop('disabled', false)
     .removeClass('btn-secondary')
     .addClass('btn-primary');
   });
  });

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
    <button type="submit" class="btn btn-primary h-50  d-flex justify-content-center align-items-center w-100"
     id="saveSelection">
     <span class="button_text">ذخیره تغیرات</span>
     <div class="loader"></div>
    </button>
    <button type="button" class="btn btn-danger h-50 w-50" data-dismiss="modal">لغو</button>
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
     <div class="d-flex flex-wrap flex-column  align-items-start gap-4 w-100">
      <div class="d-flex align-items-center w-100">
       <x-my-check :isChecked="false" id="count-label-modal" day="" />
       <div class="input-group position-relative mx-2">
        <label class="label-top-input-special-takhasos">نوبت ها </label>
        <input type="text" value="" class="form-control   text-center h-50 border-radius-0"
         name="appointment-count">
        <div class="input-group-append count-span-prepand-style"><span class="input-group-text px-2">نوبت</span>
        </div>
       </div>
      </div>
      <div class="d-flex align-items-center mt-4 w-100">
       <x-my-check :isChecked="false" id="time-label-modal" day="" />
       <div class="input-group position-relative mx-2">
        <label class="label-top-input-special-takhasos"> هر نوبت </label>
        <input type="text" value="" class="form-control   text-center h-50 border-radius-0"
         name="time-count">
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
