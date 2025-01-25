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
 <!-- Leaflet -->
 <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
 <!-- فونت آیکون -->
 <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@fortawesome/fontawesome-free@6.2.0/css/all.min.css"
  crossorigin="anonymous" referrerpolicy="no-referrer" />
</head>

<body>
 <header class="bg-light text-dark p-3 text-left my-shodow">
  <h5>محل مطب من</h5>
 </header>
 <div class="d-flex w-100 justify-content-center">
  <div class="my-container-fluid mt-2 border-radius-8 d-flex w-100 justify-content-center">
   <div class="row d-flex w-100 justify-content-center">
    <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 border-radius-8">
     <div class="card mt-3 shadow">
      <div class="card-body">
       <div id="searchContainer" class="text-center">
        <input id="searchInput" type="text" placeholder="جستجوی مکان...">
        <div id="searchResults" class="search-results"></div>
       </div>
       <div id="map" style="height: 400px; width: 100%;"></div>
       <p class="text-start font-weight-bold mt-3">محل مطب خود را از روی نقشه انتخاب کنید:</p>
       <div class="input-group">
        <input type="text" class="my-form-control w-100" placeholder="آدرس شما" readonly data-toggle="modal"
         data-target="#addressModalCenter">
        <div class="modal fade" id="addressModalCenter" tabindex="-1" role="dialog"
         aria-labelledby="addressModalCenterLabel" aria-hidden="true">
         <div class="modal-dialog modal-dialog-centered" role="document">
          <div class="modal-content border-radius-8">
           <div class="modal-header">
            <h5 class="modal-title" id="addressModalCenterLabel">ثبت آدرس</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
             <span aria-hidden="true">&times;</span>
            </button>
           </div>
           <div class="modal-body">
            <textarea style="height: 90px !important" placeholder="تهران,آزادی" name="" id="" cols="1"
             rows="1" class="my-form-control-light w-100"></textarea>
            <div class="w-100">
             <button type="button" class="btn btn-primary h-50 w-100" onclick="registerAddress()">ثبت</button>
            </div>
           </div>
          </div>
         </div>
        </div>
        <script>
         function registerAddress() {
          var address = document.getElementById('addressInput').value;
          // کد ثبت آدرس را اینجا بنویسید
          alert('آدرس شما با موفقیت ثبت شد: ' + address);
         }
        </script>
        <div class="mt-3 w-100">
         <button class="btn btn-primary h-50 w-100" type="button" data-toggle="modal" data-target="#doneModal">انجام
          شد</button>
        </div>
       </div>
      </div>
     </div>
    </div>
   </div>
  </div>
 </div>
 <div class="modal fade" id="doneModal" tabindex="-1" role="dialog" aria-labelledby="doneModalLabel"
  aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered" role="document">
   <div class="modal-content border-radius-8">
    <div class="modal-header">
     <h5 class="modal-title fs-6 font-weight-bold" id="doneModalLabel">اطلاعات تماس مطب</h5>
     <button type="button" class="close" data-dismiss="modal" aria-label="Close">
      <span aria-hidden="true">&times;</span>
     </button>
    </div>
    <div class="modal-body">
     <div class="form-group position-relative" id="phoneGroup1">
      <label class="label-top-input-special-takhasos" for="clinicPhone1">شماره تلفن مطب</label>
      <div class="input-group">
       <input type="text" class="form-control h-50 border-radius-4" id="clinicPhone1"
        placeholder="شماره تلفن مطب 1">
       <div class="input-group-append">
        <button class="btn btn-danger" type="button" id="removeButton1" style="display: none;">
         <img src="{{ asset('dr-assets/icons/trash.svg') }}" alt="">
        </button>
       </div>
      </div>
     </div>
     <div id="phoneInputs"></div>
     <div class="form-group" id="addPhoneLink">
      <a href="#" class="font-size-13 text-decoration-underline" onclick="addPhone()"
       id="addPhoneButton">افزودن شماره
       تلفن جدید</a>
     </div>
     <div class="form-group position-relative mt-3">
      <label class="label-top-input-special-takhasos" for="secretaryPhone">شماره موبایل منشی</label>
      <input type="text" class="form-control h-50 border-radius-4" id="secretaryPhone"
       placeholder="شماره موبایل منشی">
     </div>
     <div class="alert alert-info font-size-13" role="alert">
      لطفا برای اطلاع رسانی نوبت های مطب شماره موبایل منشی خود را وارد کنید
     </div>
     <div class="mt-1">
      <button type="button" class="btn btn-primary w-100 h-50" onclick="saveInfo()">ذخیره</button>
     </div>
    </div>
   </div>
  </div>
 </div>
 @include('dr.panel.layouts.partials.scripts')
 <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
 <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
 <script src="https://unpkg.com/leaflet-control-geocoder/dist/Control.Geocoder.js"></script>
 <script>
  let phoneCount = 1;

  function addPhone() {
   if (phoneCount >= 3) {
    Swal.fire({
     icon: 'warning',
     title: 'حداکثر تعداد شماره تلفن',
     text: 'شما نمی‌توانید بیشتر از ۳ شماره تلفن مطب اضافه کنید.'
    });
    return;
   }
   phoneCount++;
   const phoneInput = `
        <div class="form-group" id="phoneGroup${phoneCount}">
          <div class="input-group position-relative">
          <label class="label-top-input-special-takhasos" for="clinicPhone${phoneCount}">شماره تلفن مطب ${phoneCount}</label>
            <input type="text" class="form-control h-50 border-radius-4" id="clinicPhone${phoneCount}" placeholder="شماره تلفن مطب ${phoneCount}">
            <div class="input-group-append">
              <button class="btn btn-danger" type="button" onclick="removePhone(${phoneCount})" id="removeButton${phoneCount}">
                        <img src="{{ asset('dr-assets/icons/trash.svg') }}" alt="">
  </button>
            </div>
          </div>
        </div>
      `;
   document.getElementById('phoneInputs').insertAdjacentHTML('beforeend', phoneInput);
   updateRemoveButtonVisibility();
  }

  function removePhone(index) {
   Swal.fire({
    title: 'آیا مطمئن هستید؟',
    text: "این شماره تلفن حذف خواهد شد!",
    icon: 'warning',
    showCancelButton: true,
    confirmButtonColor: '#d33',
    cancelButtonColor: '#3085d6',
    confirmButtonText: 'بله، حذف کن!'
   }).then((result) => {
    if (result.isConfirmed) {
     const phoneInputGroup = document.getElementById(`phoneGroup${index}`);
     phoneInputGroup.remove();
     phoneCount--;
     updateRemoveButtonVisibility();
    }
   });
  }

  function updateRemoveButtonVisibility() {
   // هیچ دکمه حذف برای ورودی اصلی وجود ندارد
   if (phoneCount === 1) {
    document.getElementById('removeButton1').style.display = 'none';
   } else {
    for (let i = 2; i <= phoneCount; i++) {
     document.getElementById(`removeButton${i}`).style.display = 'inline-block';
    }
   }
   // نمایش یا پنهان کردن لینک افزودن شماره تلفن
   const addPhoneLink = document.getElementById('addPhoneLink');
   if (phoneCount >= 3) {
    addPhoneLink.style.display = 'none'; // پنهان کردن لینک افزودن
   } else {
    addPhoneLink.style.display = 'block'; // نمایش لینک افزودن
   }
  }
 </script>
 <script>
  document.addEventListener("DOMContentLoaded", function () {
      // مقداردهی اولیه نقشه
      var map = L.map('map').setView([35.6892, 51.3890], 13);
      L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        maxZoom: 19,
        attribution: '© OpenStreetMap'
      }).addTo(map);

      var marker = L.marker([35.6892, 51.3890], { draggable: true }).addTo(map);

      // جستجوی آدرس با استفاده از Nominatim
      var searchInput = document.getElementById('searchInput');
      var searchResults = document.getElementById('searchResults');

      searchInput.addEventListener('input', function () {
        var query = this.value;
        if (query.length > 2) {
          fetch(`https://nominatim.openstreetmap.org/search?format=json&q=${query}`)
            .then(response => response.json())
            .then(data => {
              searchResults.innerHTML = '';
              data.forEach(result => {
                var li = document.createElement('li');
                li.className = 'list-group-item';
                li.textContent = result.display_name;
                li.addEventListener('click', function () {
                  var lat = parseFloat(result.lat);
                  var lon = parseFloat(result.lon);
                  marker.setLatLng([lat, lon]);
                  map.setView([lat, lon], 15);
                  document.querySelector('.my-form-control').value = result.display_name;
                  searchResults.innerHTML = '';
                });
                searchResults.appendChild(li);
              });
            });
        } else {
          searchResults.innerHTML = '';
        }
      });

      // کلیک روی نقشه برای تغییر مکان مارکر
      map.on('click', function (e) {
        var lat = e.latlng.lat;
        var lng = e.latlng.lng;
        marker.setLatLng([lat, lng]);
        fetch(`https://nominatim.openstreetmap.org/reverse?format=json&lat=${lat}&lon=${lng}`)
          .then(response => response.json())
          .then(data => {
            document.querySelector('.my-form-control').value = data.display_name;
          });
      });
    });
 </script>
</body>

</html>
