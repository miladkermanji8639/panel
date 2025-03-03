@extends('admin.content.layouts/layoutMaster')
@section('title', 'بیمه ها ')
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
    <span class="text-muted fw-light"> مدیریت بیمه ها /</span>
    مدیریت بیمه های {{ $doctorName }}
     </h4>
  <div class="border bg-white p-3">
    @livewire('dr.panel.insurance.insurance-component')

  </div>
    </div>

@endsection
