@extends('admin.content.layouts/layoutMaster')

@section('title', 'افزودن بسته جدید ')

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

       <h1 class="m-n font-thin h3">بسته ها</h1>


      </div>
    <div class="wrapper-md">
        @livewire('membership-fee-form')
    </div>
     </div>
@endsection
