@extends('admin.content.layouts/layoutMaster')

@section('title', 'ویرایش درگاه پرداخت - {{ $gateway->title }}')

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
        <div class="container-fluid py-4">
            <div class="card shadow-lg border-0 rounded-3">
                <div
                    class="card-header bg-gradient-primary text-white d-flex align-items-center justify-content-between px-4 py-3">
                    <div class="d-flex align-items-center">
                        <i class="fas fa-money-check-alt me-3"></i>
                        <h5 class="mb-0 fw-bold">ویرایش درگاه: {{ $gateway->title }}</h5>
                    </div>
                    <a href="{{ route('admin.Dashboard.payment_gateways.index') }}"
                        class="btn btn-outline-light btn-sm rounded-pill">
                        <i class="fa fa-arrow-right me-2"></i> بازگشت
                    </a>
                </div>
                <div class="card-body p-4">
                    <form action="{{ route('admin.payment_gateways.update', $gateway->name) }}" method="POST" class="row g-3">
                        @csrf
                        @method('PUT')
                        <div class="col-md-6">
                            <div class="card bg-light border-0 shadow-sm rounded-3 p-3">
                                <div class="d-flex align-items-center mb-3">
                                    <div class="gateway-logo rounded-circle shadow-sm me-3"
                                        style="background-image: url('{{ $gateway->logo }}'); width: 50px; height: 50px; background-size: cover; background-position: center; border: 3px solid #dee2e6;"
                                        data-default-logo="https://cdn-icons-png.flaticon.com/512/888/888879.png">
                                    </div>
                                    <h6 class="mb-0 fw-bold text-dark">{{ $gateway->title }}</h6>
                                </div>
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" id="isActive" name="is_active" value="1" {{ $gateway->is_active ? 'checked' : '' }}>
                                    <label class="form-check-label" for="isActive">وضعیت:
                                        {{ $gateway->is_active ? 'فعال' : 'غیرفعال' }}</label>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card bg-light border-0 shadow-sm rounded-3 p-3">
                                <label for="settings" class="form-label fw-bold text-dark">تنظیمات (JSON)</label>
                                <textarea dir="ltr" class="form-control" id="settings" name="settings" rows="8"
                                    placeholder="تنظیمات را به‌صورت JSON وارد کنید"
                                    style="resize: vertical; font-family: monospace;">{{ json_encode(json_decode($gateway->settings), JSON_PRETTY_PRINT) }}</textarea>
                                <small class="text-muted mt-2 d-block">مثال: {"merchant_id": "xxxx", "sandbox": true}</small>
                            </div>
                        </div>
                        <div class="col-12 text-end mt-4">
                            <button type="submit" class="btn btn-primary btn-lg  px-5">
                                <i class="fa fa-save me-2"></i> ذخیره تغییرات
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

    <script>
        $(document).ready(function () {
            // چک کردن لوگو و جایگزینی با پیش‌فرض اگه لود نشد
            var $logo = $('.gateway-logo');
            var originalUrl = $logo.css('background-image').replace('url("', '').replace('")', '');
            var defaultUrl = $logo.data('default-logo');

            var img = new Image();
            img.onload = function () {
            };
            img.onerror = function () {
                $logo.css('background-image', 'url("' + defaultUrl + '")');
            };
            img.src = originalUrl;
        });
    </script>
@endsection