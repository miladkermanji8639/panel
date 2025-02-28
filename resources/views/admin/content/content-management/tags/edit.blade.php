@extends('admin.content.layouts.layoutMaster')

@section('title', 'ویرایش تگ')

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
 @livewire('admin.content-management.tags.tag-edit', ['id' => $id])
@endsection