<!DOCTYPE html>
<html lang="fa">

<head>
 <meta charset="UTF-8">
 <meta name="viewport" content="width=device-width, initial-scale=1.0">
 <title>ساعت کاری</title>
 @include('dr.panel.layouts.partials.head-tags')
 <link rel="stylesheet" href="{{ asset('dr-assets/panel/css/bootstrap.min.css') }}">
 <link rel="stylesheet" href="{{ asset('dr-assets/panel/css/style.css') }}">
 <link rel="stylesheet" href="{{ asset('dr-assets/panel/css/panel.css') }}">
 <link rel="stylesheet" href="{{ asset('dr-assets/panel/profile/edit-profile.css') }}">

 <link rel="stylesheet" href="{{ asset('dr-assets/panel/css/doctors-clinic/duration/duration.css') }}">
 <link rel="stylesheet" href="{{ asset('dr-asset/panel/css/toastify/toastify.min.css') }}">
</head>

<body dir="rtl">
 <header class="bg-light text-dark p-3 text-left my-shadow">
  <h5 class="card-title text-center font-weight-bold">ساعت کاری</h5>
 </header>

 <div class="d-flex w-100 justify-content-center mt-3">
  <div class="my-container-fluid mt-2 border-radius-8 d-flex justify-content-center">
   <div class="row justify-content-center">
    <div class="">
     <div class="card p-4">
      <h5 class="text-start font-weight-bold">مدت زمان هر نوبت بیمار در مطب شما چقدر است؟</h5>
      <div class="d-flex flex-wrap mt-4 gap-10 justify-content-end my-768px-styles-day-and-times">
       <div class="" tabindex="0" role="button">
        <span class="badge-time-styles-plus">
         <svg xmlns="http://www.w3.org/2000/svg" fill="none" stroke="currentColor" stroke-width="2"
          stroke-linecap="round" stroke-linejoin="round"
          class="plasmic-default__svg plasmic_all__FLoMj PlasmicDuration_svg__l9OeP__cvsVD lucide lucide-plus"
          viewBox="0 0 24 24" height="20px" width="20px" role="img" type="button" aria-haspopup="dialog"
          aria-expanded="false" aria-controls="radix-:r7:" data-state="closed">
          <path d="M5 12h14m-7-7v14"></path>
         </svg>
        </span>
        <span class=""></span>
       </div>
       <div class="" tabindex="0" role="button"><span class="badge-time-styles">5 دقیقه</span>
        <span class=""></span>
       </div>
       <div class="" tabindex="0" role="button">
        <span class="badge-time-styles">
         10 دقیقه

        </span>
        <span class=""></span>
       </div>
       <div class="" tabindex="0" role="button">
        <span class="badge-time-styles">15 دقیقه</span>
        <span class="">
        </span>
       </div>
       <div class="" tabindex="0" role="button">
        <span class="badge-time-styles">20 دقیقه</span>
        <span class="">
        </span>
       </div>
       <div class="" tabindex="0" role="button">
        <span class="badge-time-styles">30 دقیقه</span>
        <span class="">
        </span>
       </div>
       <div class="" tabindex="0" role="button">
        <span class="badge-time-styles active-hours">60 دقیقه<svg width="16" height="16" viewBox="0 0 16 16"
          fill="#7c82fc">
          <path fill-rule="evenodd" clip-rule="evenodd"
           d="M13.8405 3.44714C14.1458 3.72703 14.1664 4.20146 13.8865 4.5068L6.55319 12.5068C6.41496 12.6576 6.22113 12.7454 6.01662 12.7498C5.8121 12.7543 5.61464 12.675 5.47 12.5303L2.13666 9.197C1.84377 8.90411 1.84377 8.42923 2.13666 8.13634C2.42956 7.84345 2.90443 7.84345 3.19732 8.13634L5.97677 10.9158L12.7808 3.49321C13.0607 3.18787 13.5351 3.16724 13.8405 3.44714Z">
          </path>
         </svg></span>
        <span class="">
        </span>
       </div>
      </div>
      <div class="alert alert-secondary text-center mt-3">
       پزشکان حرفه‌ای به‌طور معمول هر ویزیتشان حدود ۱۵ دقیقه طول می‌کشد. اگر در مطب پروسیجرهای درمان انجام می‌دهید
       این زمان را طولانی‌تر کنید.
      </div>
      <form id="workingHoursForm">
       @csrf
       <input type="hidden" name="clinic_id" value="{{ $clinicId }}">
       <input type="hidden" name="doctor_id" value="{{ $doctorId }}">
       <input type="hidden" id="appointment_duration" name="appointment_duration" value="">


       <h6 class="mt-4">آیا با سایت‌های دیگر نوبت‌دهی اینترنتی همکاری دارید؟*</h6>
       <div class="form-check mt-2">
        <input class="form-check-input" type="radio" name="collaboration" id="yesOption" value="1">
        <label class="form-check-label" for="yesOption">
         بله، و می‌خواهم زمان نوبت‌های به نوبه با آن‌ها تداخل نداشته باشد.
        </label>
       </div>
       <div class="form-check mt-2">
        <input class="form-check-input" type="radio" name="collaboration" id="noOption" value="0" checked>
        <label class="form-check-label" for="noOption">
         نه، فقط از طریق به نوبه نوبت‌دهی را انجام می‌دهیم.
        </label>
       </div>

       <div class="text-center mt-4">
        <button id="saveButton" type="submit" class="btn btn-primary w-100 h-50">ثبت ساعت کاری</button>
       </div>
      </form>
     </div>
    </div>
   </div>
  </div>
 </div>

 @include('dr.panel.layouts.partials.scripts')
 <script src="{{ asset('dr-assets/panel/js/turn/scehedule/sheduleSetting/workhours/workhours.js') }}"></script>
 <script src="{{ asset('dr-asset/panel/js/toastify/toastify.min.js') }}"></script>
<script>
 // انتخاب بج‌ها (Badge Selection)
 document.querySelectorAll('.badge-time-styles').forEach(element => {
  element.addEventListener('click', () => {
   // پاک کردن حالت انتخاب قبلی
   // افزودن کلاس انتخاب به المان کلیک شده
   // تنظیم مقدار مدت زمان در فیلد مخفی
   const duration = element.textContent.trim().replace(' دقیقه', '');
   document.getElementById('appointment_duration').value = duration;
  });
 });

 // ذخیره اطلاعات فرم
 document.getElementById('workingHoursForm').addEventListener('submit', async (e) => {
  e.preventDefault();

  const form = e.target;
  const formData = new FormData(form);

  // تغییر دکمه به حالت لودینگ
  const saveButton = document.getElementById('saveButton');
  saveButton.disabled = true;
  saveButton.innerHTML = `
      <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
      در حال ثبت...
    `;

  try {
   const response = await fetch("{{ route('duration.store') }}", {
    method: "POST",
    headers: {
     "X-CSRF-TOKEN": document.querySelector('input[name="_token"]').value,
    },
    body: formData,
   });

   const data = await response.json();

   if (data.success) {
    Toastify({
     text: data.message,
     duration: 3000,
     close: true,
     gravity: "top",
     position: "right",
     backgroundColor: "#4caf50",
    }).showToast();
    location.href = "{{ route('activation.workhours.index',$clinicId) }}"
   } else {
    Toastify({
     text: data.message || "مشکلی در ذخیره اطلاعات رخ داد.",
     duration: 3000,
     close: true,
     gravity: "top",
     position: "right",
     backgroundColor: "#f44336",
    }).showToast();
   }
  } catch (error) {
   Toastify({
    text: "خطا در ارتباط با سرور.",
    duration: 3000,
    close: true,
    gravity: "top",
    position: "right",
    backgroundColor: "#f44336",
   }).showToast();
  } finally {
   // بازگرداندن دکمه به حالت اولیه
   saveButton.disabled = false;
   saveButton.innerHTML = "ثبت ساعت کاری";
  }
 });
</script>


</body>

</html>
