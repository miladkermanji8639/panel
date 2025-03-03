@extends('admin.content.layouts/layoutMaster')

@section('title', 'بیمارستان ها ')

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
  <div>
    <livewire:admin.hospitals.hospitals-management.hospital-list />
  </div>
@endsection
