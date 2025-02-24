@extends('admin.content.layouts/layoutMaster')

@section('title', 'بسته ها ')

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


   <h4 class="py-3 mb-4">
    <span class="text-muted fw-light">بسته های حق عضویت /</span>
    لیست بسته های حق عضویت
   </h4>


   <div class="card">
    <div class="card-header">
     <h5 class="card-title mb-0">لیست بسته های حق عضویت</h5>

    </div>
    <div class="card-datatable table-responsive">
     @livewire('admin.dashboard.usership.usership-fee-component')
    </div>
   </div>


  </div>
  <div class="content-backdrop fade"></div>
 </div>
@endsection
