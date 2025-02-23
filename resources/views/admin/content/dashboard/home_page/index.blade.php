@extends('admin.content.layouts.layoutMaster')

@section('title', ' برترین پزشکان و مشاوران سایت ')

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
        <span class="text-muted fw-light">پزشکان برتر /</span>
        لیست پزشکان برتر
       </h4>


       <div class="card">
    @livewire('admin.dashboard.search-best-doctors')

       </div>


      </div>

      <div class="content-backdrop fade"></div>
     </div>
@endsection
