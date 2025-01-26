<!DOCTYPE html>
<html lang="fa">

<head>
 <meta charset="UTF-8">
 <meta name="viewport" content="width=device-width, initial-scale=1.0">
 <title>محل مطب من</title>
 @include('dr.panel.layouts.partials.head-tags')
 <link rel="stylesheet" href="{{ asset('dr-assets/panel/css/bootstrap.min.css') }}">
 <link rel="stylesheet" href="{{ asset('dr-assets/panel/css/style.css') }}">
 <link rel="stylesheet" href="{{ asset('dr-assets/panel/css/panel.css') }}">
 <link rel="stylesheet" href="{{ asset('dr-assets/panel/profile/edit-profile.css') }}">
 <link rel="stylesheet" href="{{ asset('dr-assets/panel/css/doctors-clininc/activation/index.css') }}">
 <link rel="stylesheet" href="{{ asset('dr-assets/panel/css/doctors-clininc/clinic/cost/cost.css') }}">
 <link rel="stylesheet" href="{{ asset('dr-asset/panel/css/toastify/toastify.min.css') }}">
 <!-- Leaflet -->
 <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
 <!-- فونت آیکون -->
 <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@fortawesome/fontawesome-free@6.2.0/css/all.min.css"
  crossorigin="anonymous" referrerpolicy="no-referrer" />
 @include('dr.panel.my-tools.loader-btn')
</head>

<body dir="rtl">
<header class="bg-light text-dark p-3 text-left my-shodow">
     <h5 class="card-title text-center font-weight-bold">مبلغ بیعانه</h5>

</header>

 <div class="d-flex w-100 justify-content-center mt-3">
 <div class="my-container-fluid-cost mt-2 border-radius-8 d-flex  justify-content-center">
  <div class="row justify-content-center">
   <div class="">
    <div class="card p-4">
     <div class="alert alert-info text-center" role="alert">
      دریافت بیعانه به هنگام ثبت نوبت اینترنتی باعث می‌شود کسانی که نوبت گرفته‌اند، مقید به حضور حتمی و به موقع در مطب
      شوند.
     </div>
     <p class="text-center font-weight-bold">همکاران شما به‌صورت میانگین مبلغ ۵۰,۰۰۰ تومان را در نظر گرفته‌اند.</p>
     <form id="depositForm">
      @csrf
         <input type="hidden" name="clinic_id" value="{{ $clinicId }}"> <!-- شناسه مطب -->
         <input type="hidden" name="doctor_id" value="{{ $doctorId }}"> <!-- شناسه دکتر -->
         <input type="hidden" id="isCustomPrice" name="is_custom_price" value="0">

      <div class="mb-3 mt-4">
       <select id="depositAmount" name="deposit_amount" class="form-control h-50" dir="rtl">
        <option value="" selected><span class="font-weight-bold">قیمت بیعانه</span></option>
        <option value="10000">۱۰,۰۰۰ تومان</option>
        <option value="20000">۲۰,۰۰۰ تومان</option>
        <option value="30000">۳۰,۰۰۰ تومان</option>
        <option value="40000">۴۰,۰۰۰ تومان</option>
        <option value="50000">۵۰,۰۰۰ تومان</option>
        <option value="100000">۱۰۰,۰۰۰ تومان</option>
        <option value="custom">قیمت دلخواه</option>
       </select>
        <!-- اینجا اینپوت مخفی قرار دارد که در زمان انتخاب گزینه "قیمت دلخواه" نمایش داده می‌شود -->
        <input type="text" name="custom_price"  id="customPrice" class="form-control mt-3 h-50 d-none" placeholder="مبلغ دلخواه را وارد کنید">
      </div>
      <div class="alert alert-warning text-center" role="alert">
       جهت دریافت مبالغ پرداختی بیعانه بیماران لطفا پس از تکمیل ثبت نام، در قسمت تنظیمات پرداخت، شماره کارت خود را وارد
       نمایید.
      </div>
       <div class="modal fade" id="confirmationModal" tabindex="-1" role="dialog" aria-labelledby="confirmationModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
         <div class="modal-content border-radius-8">
          <div class="modal-body">
           <ul class="list-unstyled">
            <li class="mt-3 font-size-14 font-weight-bold">• بیمار در صورتی موفق به اخذ نوبت می‌شود که بیعانه را پرداخت نماید.
            </li>
            <li class="mt-3 font-size-14 font-weight-bold">• بیماری که در لیست بیماران مشاهده می‌کنید، تمامی پرداخت بیعانه را
             انجام داده‌است.</li>
            <li class="mt-3 font-size-14 font-weight-bold">• در صورتی که بیمار نوبت خود را تا ۲۴ ساعت پیش از ساعت نوبت لغو
             نماید، وجه پرداختی بیمار استرداد می‌گردد.</li>
           </ul>
       
       
           <div class="w-100">
            <button type="button" id="saveButton" class="btn btn-outline-primary w-100 h-50">ذخیره</button>
       
           </div>
          </div>
         </div>
        </div>
       </div>
      <div class="d-flex justify-content-between w-100 gap-4">
       <button type="button" class="btn btn-primary h-50 w-50" data-toggle="modal" data-target="#confirmationModal">ادامه</button>
       <button type="button" class="btn btn-outline-secondary h-50 w-50">انصراف</button>
      </div>
      <div id="depositList" class="mt-4">
       <!-- لیست بیعانه‌ها اینجا لود می‌شود -->
      </div>

     </form>
     <!-- Modal -->
    
    </div>
   </div>
  </div>
 </div>
 </div>
 @include('dr.panel.layouts.partials.scripts')
 <script src="{{ asset('dr-asset/panel/js/toastify/toastify.min.js') }}"></script>
 <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>

 function loadDeposits() {
  fetch("{{ route('cost.list', ['clinic_id' => $clinicId]) }}")
   .then(response => response.json())
   .then(data => {
    const depositList = document.getElementById('depositList');
    depositList.innerHTML = ''; // پاک کردن لیست قبلی

    if (data.length > 0) {
     data.forEach(item => {
      const formattedAmount = formatNumber(item.deposit_amount); // فرمت سه‌رقمی
      const depositItem = `
            <div class="d-flex justify-content-between align-items-center p-2 border rounded mb-2">
                <span> مبلغ بیعانه برای هر نوبت ${formattedAmount} تومان</span>
                <button type="button" class="btn btn-light btn-sm" onclick="deleteDeposit(${item.id})">
                    <img src="{{ asset('dr-assets/icons/trash.svg') }}" alt="حذف">
                </button>
            </div>
        `;
      depositList.insertAdjacentHTML('beforeend', depositItem);
     });
    }
 else {
     depositList.innerHTML = '<p class="text-center">هیچ مبلغ بیعانه‌ای ثبت نشده است.</p>';
    }
   })
   .catch(error => {
    console.error('Error loading deposits:', error);
   });
 }
 function formatNumber(number) {
  const formatted = number.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
  // حذف ".00" در صورت وجود
  return formatted.replace(/\.00$/, "");
 }


 function deleteDeposit(id) {
  Swal.fire({
   title: "آیا مطمئن هستید؟",
   text: "این بیعانه حذف خواهد شد!",
   icon: "warning",
   showCancelButton: true,
   confirmButtonColor: "#d33",
   cancelButtonColor: "#3085d6",
   confirmButtonText: "بله، حذف کن!",
   cancelButtonText: "لغو",
  }).then(result => {
   if (result.isConfirmed) {
    fetch("{{ route('cost.delete') }}", {
     method: "POST",
     headers: {
      "X-CSRF-TOKEN": document.querySelector('input[name="_token"]').value,
      "Content-Type": "application/json",
     },
     body: JSON.stringify({ id: id }),
    })
     .then(response => response.json())
     .then(data => {
      if (data.success) {
       Swal.fire("حذف شد!", "بیعانه با موفقیت حذف شد.", "success");
       loadDeposits(); // بارگذاری مجدد لیست بیعانه‌ها
      } else {
       Swal.fire("خطا!", "مشکلی در حذف بیعانه رخ داد.", "error");
      }
     })
     .catch(error => {
      Swal.fire("خطا!", "مشکلی در ارتباط با سرور رخ داد.", "error");
     });
   }
  });
 }

 // لود کردن لیست بیعانه‌ها هنگام بارگذاری صفحه
 document.addEventListener("DOMContentLoaded", loadDeposits);

 document.getElementById('depositAmount').addEventListener('change', function () {
  const customPrice = document.getElementById('customPrice');
  const isCustomPrice = document.getElementById('isCustomPrice');

  if (this.value === 'custom') {
   customPrice.classList.remove('d-none');
   customPrice.setAttribute('name', 'deposit_amount');
   isCustomPrice.value = 1; // تنظیم به true
  } else {
   customPrice.classList.add('d-none');
   customPrice.removeAttribute('name');
   isCustomPrice.value = 0; // تنظیم به false
  }
 });



  document.getElementById('saveButton').addEventListener('click', function () {
    const form = document.getElementById('depositForm');
    const formData = new FormData(form);
    fetch("{{ route('cost.store') }}", {
     method: "POST",
     headers: {
      "X-CSRF-TOKEN": document.querySelector('input[name="_token"]').value,
     },
     body: formData,
    })
     .then(response => response.json())
     .then(data => {
      if (data.success) {
       Swal.fire({
        icon: "success",
        title: "ذخیره موفقیت‌آمیز",
        text: data.message,
       });
       loadDeposits();
       location.href = "{{ route('duration.index',$clinicId) }}"
      } else {
       Swal.fire({
        icon: "error",
        title: "خطا",
        text: data.message, // نمایش پیام خطا از سرور
       });
      }
     })
     .catch(error => {
      Swal.fire({
       icon: "error",
       title: "خطا",
       text: "مشکلی در ارسال اطلاعات رخ داد.",
      });
     });
   });


</script>

</body>

</html>