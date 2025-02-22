@extends('admin.content.layouts/layoutMaster')

@section('title', 'تعرفه حق عضویت پزشکان')

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





  <div class="card">
  <div class="card-header">
    <h5 class="card-title mb-0">لیست تعرفه‌ها</h5>
    
  </div>
  
  <div class="card-datatable table-responsive">
    @livewire('membership-fee-component')
  </div>
  </div>


  </div>
  <!-- / Content -->

  <!-- Footer -->
  <!-- Footer-->

  <!--/ Footer-->
  <!-- / Footer -->
  <div class="content-backdrop fade"></div>
   </div>


@endsection
