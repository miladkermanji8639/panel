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
   <style>
    .panel-default {
     border-color: #e2e2e2;
    }

    .panel {
     border-radius: 10px;
     border: 1px solid;
     overflow: hidden;
     right: 0;
     margin-top: 5px;
     padding: 8px;
    }

    .panel-primary {
     border-color: #2d67a7;
     background-color: rgba(240, 246, 251, .47);
    }
   </style>
   <div class="app-content-body">

    <div class="bg-white-only lter b-b wrapper-md clrfix">

     <h1 class="m-n h3 font-thin">پزشک ها</h1>

    </div>
    <div class="wrapper-md">
   @livewire('admin.dashboard.create-best-doctor')

    </div>
   </div>
@endsection
