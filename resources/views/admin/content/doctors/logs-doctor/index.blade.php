@extends('admin.content.layouts.layoutMaster')

@section('title', 'لاگ پزشکان')

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
  @livewire('admin.doctors.logs-doctor')
@endsection