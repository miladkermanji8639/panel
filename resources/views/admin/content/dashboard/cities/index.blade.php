@extends('admin.content.layouts/layoutMaster')
@section('title', 'استانها ')
@section('vendor-style')
 @vite(['resources/assets/vendor/libs/apex-charts/apex-charts.scss'])
@endsection
@section('vendor-script')
 @vite(['resources/assets/vendor/libs/apex-charts/apexcharts.js'])
@endsection
@section('page-script')

 @vite(['resources/assets/js/dashboards-crm.js'])
@endsection
@section('content')
    <div class="content-wrapper">
     <!-- Content -->
     <div class="flex-grow-1  container-fluid">
    <h4 class="mb-4">
      <span class="text-muted fw-light">ناحیه ها /</span>
      لیست استانها
    </h4>
    <div class="card">
      <div class="card-header">
       <h5 class="card-title mb-0">لیست استانها</h5>
      </div>
      @livewire('search-zones')

    </div>
     </div>



     <div class="content-backdrop fade"></div>
    </div>
    <script>
    document.addEventListener('DOMContentLoaded', function () {
      Livewire.on('show-toastr', (data) => {
      toastr.options = {
        progressBar: true,
        positionClass: "toast-top-right", // نمایش در سمت راست بالا
        timeOut: 3000 // زمان نمایش
      };

      if (data.type === 'success') {
        toastr.success(data.message);
      } else {
        toastr.warning(data.message);
      }
      });
    });
    </script>

@endsection
