@extends('admin.content.layouts.layoutMaster')

@section('title', 'جزئیات پرداخت')

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
                    <i class="fas fa-info-circle fs-4 text-white animate-bounce"></i>
                    <h4 class="mb-0 fw-bold text-white">جزئیات پرداخت</h4>
                </div>
                <div class="text-white fw-medium fs-6">مشاهده اطلاعات پرداخت و نوبت</div>
            </div>
        </header>

        @livewire('admin.doctors.order-visit-details', ['orderVisitId' => $orderVisitId])
    </div>
@endsection