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

@section('content')
  <div class="app-content-body">

    <div class="bg-white-only lter b-b wrapper-md clrfix d-flex justify-content-between">

    <h1 class="m-n font-thin h3">تخصص ها</h1>

  <a href="{{ route('admin.Dashboard.specialty.index') }}"
       class="btn btn-warning">بازگشت</a>
    </div>
    <div class="wrapper-md w-100">

  <livewire:admin.dashboard.specialties.specialty-create />

    </div>
  </div>

@endsection
