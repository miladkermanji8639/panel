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
        <span class="badge-time-styles">60 دقیقه</span>
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
  document.addEventListener("DOMContentLoaded", function () {
      const badgeElements = document.querySelectorAll('.badge-time-styles');
      const appointmentInput = document.getElementById('appointment_duration');
      const saveButton = document.getElementById('saveButton');

      // ✅ انتخاب و حذف انتخاب زمان
      badgeElements.forEach(element => {
        element.addEventListener('click', function () {
          const selectedDuration = this.textContent.trim().replace(' دقیقه', '');

          // بررسی اینکه این گزینه قبلاً انتخاب شده یا نه
          if (appointmentInput.value === selectedDuration) {
            appointmentInput.value = ""; // حذف انتخاب
            this.classList.remove('selected'); // استایل غیرفعال کردن
          } else {
            appointmentInput.value = selectedDuration;

            // حذف کلاس انتخاب از بقیه گزینه‌ها
            badgeElements.forEach(el => el.classList.remove('selected'));
            this.classList.add('selected');
          }
        });
      });

      // ✅ ثبت فرم و بررسی خطا در انتخاب نوبت
      document.getElementById('workingHoursForm').addEventListener('submit', async (e) => {
        e.preventDefault();

        if (!appointmentInput.value) {
          toastr.error("لطفاً یک مدت زمان برای نوبت انتخاب کنید.");
          return;
        }

        const form = e.target;
        const formData = new FormData(form);

        saveButton.disabled = true;
        saveButton.innerHTML = `
      <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
      در حال ثبت...
    `;

        try {
          const response = await fetch("{{ route('duration.store') }}", {
            method: "POST",
            headers: { "X-CSRF-TOKEN": document.querySelector('input[name="_token"]').value },
            body: formData,
          });

          const data = await response.json();

          if (data.success) {
            toastr.success(data.message);
            location.href = "{{ route('activation.workhours.index', $clinicId) }}";
          } else {
            toastr.error(data.message || "مشکلی در ذخیره اطلاعات رخ داد.");
          }
        } catch (error) {
          toastr.error("خطا در ارتباط با سرور.");
        } finally {
          saveButton.disabled = false;
          saveButton.innerHTML = "ثبت ساعت کاری";
        }
      });
    });

</script>


</body>

</html>
c