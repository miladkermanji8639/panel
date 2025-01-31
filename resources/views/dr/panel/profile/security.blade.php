@extends('dr.panel.layouts.master')

@section('styles')
<link type="text/css" href="{{ asset('dr-assets/panel/css/panel.css') }}" rel="stylesheet" />
<link type="text/css" href="{{ asset('dr-assets/panel/profile/edit-profile.css') }}" rel="stylesheet" />
<link type="text/css" href="{{ asset('dr-assets/panel/css/profile/security.css') }}" rel="stylesheet" />
@endsection

@section('site-header')
{{ 'به نوبه | پنل دکتر' }}
@endsection

@section('content')
@section('bread-crumb-title', 'امنیت')

<div class="subuser-content d-flex w-100 justify-content-center">
  <div class="subuser-content-wrapper">

    <!-- تاریخچه ورود منشی -->
  <div class="card">
    <div class="card-header">📜 تاریخچه ورود منشی</div>
    <div class="card-body" id="secretaryLogsContainer">
      @include('dr.panel.profile.partials.secretary_logs')
    </div>
  </div>
  
  <div class="card">
    <div class="card-header">📜 تاریخچه ورود دکتر</div>
    <div class="card-body" id="doctorLogsContainer">
      @include('dr.panel.profile.partials.doctor_logs')
    </div>
  </div>


  </div>
</div>
@endsection

@section('scripts')
<script src="{{ asset('dr-assets/panel/jalali-datepicker/run-jalali.js') }}"></script>
<script src="{{ asset('dr-assets/panel/js/dr-panel.js') }}"></script>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
  $(document).ready(function () {
    function loadLogs(page = 1) {
      $.ajax({
        url: "{{ route('dr-edit-profile-security') }}?page=" + page,
        type: 'GET',
        success: function (response) {
          $('#doctorLogsContainer').html(response.doctorLogs);
          $('#secretaryLogsContainer').html(response.secretaryLogs);
        }
      });
    }

    $(document).on('click', '.pagination-links a', function (e) {
      e.preventDefault();
      let page = $(this).attr('href').split('page=')[1];
      loadLogs(page);
    });

    $(document).on('click', '.delete-log', function () {
      let logId = $(this).data('id');
      let row = $(this).closest('tr');

      Swal.fire({
        title: 'آیا مطمئن هستید؟',
        text: "این عمل قابل بازگشت نیست!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'بله، حذف شود!',
        cancelButtonText: 'لغو'
      }).then((result) => {
        if (result.isConfirmed) {
          $.ajax({
            url: "{{ route('delete-log', ':id') }}".replace(':id',logId),
            type: 'DELETE',
            headers: {
              'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function () {
              row.remove();
              loadLogs();
              toastr.success('تارخچه مورد نظر با موفقیت حذف شد.');
            }
          });
        }
      });
    });
  });
  
</script>
<script>
  $(document).ready(function () {
    function loadDoctorLogs(page) {
      $.ajax({
        url: "{{ route('dr-get-doctor-logs') }}?page=" + page,
        type: 'GET',
        success: function (response) {
          $('#doctorLogsContainer').html(response.doctorLogsHtml); // تغییر نام متغیر به `doctorLogsHtml`
        }
      });
    }

    function loadSecretaryLogs(page) {
      $.ajax({
        url: "{{ route('dr-get-secretary-logs') }}?page=" + page,
        type: 'GET',
        success: function (response) {
          $('#secretaryLogsContainer').html(response.secretaryLogsHtml); // تغییر نام متغیر به `secretaryLogsHtml`
        }
      });
    }

    // پیجینیشن دکتر
    $(document).on('click', '#doctorLogsContainer .pagination-links a', function (e) {
      e.preventDefault();
      let page = $(this).attr('href').split('page=')[1];
      loadDoctorLogs(page);
    });

    // پیجینیشن منشی
    $(document).on('click', '#secretaryLogsContainer .pagination-links a', function (e) {
      e.preventDefault();
      let page = $(this).attr('href').split('page=')[1];
      loadSecretaryLogs(page);

    });
  });
</script>


<script>
  var appointmentsSearchUrl = "{{ route('search.appointments') }}";
  var updateStatusAppointmentUrl = "{{ route('updateStatusAppointment', ':id') }}";
</script>
@endsection