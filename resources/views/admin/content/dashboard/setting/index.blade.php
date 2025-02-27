@extends('admin.content.layouts/layoutMaster')


@section('vendor-style')
 @vite(['resources/assets/vendor/libs/apex-charts/apex-charts.scss'])
@endsection

@section('vendor-script')
 @vite(['resources/assets/vendor/libs/apex-charts/apexcharts.js'])
@endsection

@section('page-script')
 @vite(['resources/assets/js/dashboards-crm.js'])
@endsection
<link href="{{ asset('admin-assets/css/old-benobe-styles/bootstrap.min.css') }}" rel="stylesheet">
<link href="{{ asset('admin-assets/css/old-benobe-styles/bootstrap-rtl.min.css') }}" rel="stylesheet">
<link href="{{ asset('admin-assets/css/old-benobe-styles/app_admin.css?v=dddmue') }}" rel="stylesheet">
<style>
 a {
  color: #333 !important;
 }
</style>
@section('content')
  <livewire:admin.dashboard.system-setting.settings-component />
@endsection
