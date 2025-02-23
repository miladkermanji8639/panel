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

    <div class="bg-white-only lter b-b wrapper-md clrfix">

     <h1 class="m-n h3 font-thin">منو ها</h1>

    </div>
    <div class="wrapper-md">
    @livewire('admin.dashboard.menu.menu-form')

    </div>
   </div>
@endsection
