@extends('admin.content.layouts/layoutMaster')

@section('title', 'تخصص ها ')

@section('vendor-style')
 @vite(['resources/assets/vendor/libs/apex-charts/apex-charts.scss'])
@endsection

@section('vendor-script')
 @vite(['resources/assets/vendor/libs/apex-charts/apexcharts.js'])
@endsection

@section('page-script')
 @vite(['resources/assets/js/dashboards-crm.js'])
@endsection

{{-- <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script> --}}

@section('content')

 <div class="content-wrapper">

  <!-- Content -->
  <div class="flex-grow-1  container-fluid">


   <h4 class="py-3 mb-4">
    <span class="text-muted fw-light">تخصص ها /</span>
    لیست تخصص ها
   </h4>


   <div class="card">
    <div class="card-header">
     <h5 class="card-title mb-0">لیست تخصص‌ها</h5>
    </div>
    @livewire('admin.dashboard.specialties.search-specialties')
   </div>



  </div>




  <div class="content-backdrop fade"></div>
 </div>
@endsection
