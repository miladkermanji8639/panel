@extends('admin.content.layouts.layoutMaster')

@section('title', 'مدیریت پزشکان')

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
    <div class="container-fluid py-1">
        <header class="glass-header p-3 rounded-3 mb-2 shadow-lg">
            <div class="d-flex align-items-center justify-content-between gap-3">
                <div class="d-flex align-items-center gap-2">
                    <i class="fas fa-user-md fs-4 text-white animate-bounce"></i>
                    <h4 class="mb-0 fw-bold text-white">مدیریت پزشکان</h4>
                </div>
                <div>
                    <a href="{{ route('admin.doctors.doctors-management.create') }}" class="btn btn-light">ایجاد پزشک</a>
                </div>
            </div>
        </header>

        @livewire('admin.tools.file-manager')
    </div>
@endsection