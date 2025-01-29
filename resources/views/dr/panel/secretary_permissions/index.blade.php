@extends('dr.panel.layouts.master')

@section('styles')
 <link rel="stylesheet" href="{{ asset('dr-assets/panel/css/panel.css') }}">
 <link rel="stylesheet" href="{{ asset('dr-assets/panel/css/turn/schedule/scheduleSetting/scheduleSetting.css') }}">
 <link rel="stylesheet" href="{{ asset('dr-assets/panel/profile/edit-profile.css') }}">
 <link rel="stylesheet" href="{{ asset('dr-assets/panel/css/secretary_options/secretary_option.css') }}">
 <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/toastr/build/toastr.min.css">
@endsection

@section('site-header', 'به نوبه | پنل دکتر')

@section('content')
@section('bread-crumb-title', 'مدیریت دسترسی‌ها')

<div class="container mt-4">
 <div class="card">
  <div class="card-header bg-primary text-white">
   <h5 class="mb-0">مدیریت دسترسی منشی‌ها</h5>
  </div>
  <div class="card-body">
   <form id="permissions-form">
    <div class="table-responsive">
     <table class="table table-bordered">
      <thead class="table-dark">
       <tr>
        <th>نام منشی</th>
        <th>دسترسی‌ها</th>
       </tr>
      </thead>
      <tbody>
       @foreach ($secretaries as $secretary)
        @php
         $savedPermissions = json_decode($secretary->permissions->permissions ?? '[]', true);
         $savedPermissions = is_array($savedPermissions) ? $savedPermissions : []; // اطمینان از آرایه بودن
        @endphp
        <tr>
         <td>{{ $secretary->first_name }} {{ $secretary->last_name }}</td>
         <td>
          <div class="form-check w-100" style="text-align: right">
           @foreach ($permissions as $permissionKey => $permissionData)
            <div class="mb-2">
             <input type="checkbox" class="form-check-input parent-permission update-permissions"
              data-secretary-id="{{ $secretary->id }}" value="{{ $permissionKey }}"
              {{ in_array($permissionKey, $savedPermissions) ? 'checked' : '' }}>
             <label class="form-check-label font-weight-bold">{{ $permissionData['title'] }}</label>
            </div>
            @if (!empty($permissionData['routes']))
             <div class="ml-3">
              @foreach ($permissionData['routes'] as $routeKey => $routeTitle)
               <div>
                <input type="checkbox" class="form-check-input child-permission update-permissions"
                 data-secretary-id="{{ $secretary->id }}" data-parent="{{ $permissionKey }}"
                 value="{{ $routeKey }}" {{ in_array($routeKey, $savedPermissions) ? 'checked' : '' }}>
                <label class="form-check-label">{{ $routeTitle }}</label>
               </div>
              @endforeach
             </div>
            @endif
           @endforeach
          </div>
         </td>
        </tr>
       @endforeach
      </tbody>
     </table>
    </div>
   </form>
  </div>
 </div>
</div>

@endsection

@section('scripts')
<script src="{{ asset('dr-assets/panel/jalali-datepicker/run-jalali.js') }}"></script>
<script src="{{ asset('dr-assets/panel/js/dr-panel.js') }}"></script>
<script src="{{ asset('dr-assets/panel/js/turn/scehedule/sheduleSetting/workhours/workhours.js') }}"></script>
<script src="https://cdn.jsdelivr.net/npm/toastr/build/toastr.min.js"></script>

<script>
 $(document).ready(function() {

  let updateTimer; // متغیر برای مدیریت debounce

  // تابع برای ارسال درخواست AJAX
  function updatePermissions(secretaryId) {
   let permissions = [];
   $('input[data-secretary-id="' + secretaryId + '"]:checked').each(function() {
    permissions.push($(this).val());
   });

   $.ajax({
    url: "{{ route('dr-secretary-permissions-update', ':id') }}".replace(':id', secretaryId),
    method: "POST",
    data: {
     permissions: permissions,
     _token: "{{ csrf_token() }}"
    },
    success: function(response) {
     if (response.success) {
      toastr.success('دسترسی‌ها با موفقیت ذخیره شد.');
     }
    },
    error: function() {
     toastr.error('مشکلی در ذخیره اطلاعات پیش آمد.');
    }
   });
  }

  // مدیریت ارتباط بین والد و فرزند
  $('.parent-permission').change(function() {
   let isChecked = $(this).is(':checked');
   let parentKey = $(this).val();
   let secretaryId = $(this).data('secretary-id');
   $(this).closest('td').find(`.child-permission[data-parent="${parentKey}"]`).prop('checked', isChecked);

   clearTimeout(updateTimer);
   updateTimer = setTimeout(() => updatePermissions(secretaryId), 500);
  });

  // بروزرسانی سطوح دسترسی با debounce
  $('.update-permissions').change(function() {
   let secretaryId = $(this).data('secretary-id');

   clearTimeout(updateTimer);
   updateTimer = setTimeout(() => updatePermissions(secretaryId), 500);
  });

 });
</script>
@endsection
