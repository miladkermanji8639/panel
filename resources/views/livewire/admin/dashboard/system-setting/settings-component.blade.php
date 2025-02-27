<div class="container-fluid py-4">
    <!-- هدر اصلی -->
    <div class="bg-light text-dark p-4 rounded-top border">
        <div class="d-flex align-items-center">
            <i class="fas fa-cog me-3"></i>
            <h5 class="mb-0 fw-bold">تنظیمات سامانه</h5>
        </div>
    </div>

    <!-- بدنه اصلی -->
    <div class="bg-white p-4 rounded-bottom shadow-sm">
        <!-- تب‌ها -->
        <ul class="nav nav-tabs mb-4 border-0">
            <li class="nav-item">
                <button class="nav-link {{ $activeTab === 'general' ? 'active' : '' }}"
                    wire:click="switchTab('general')">تنظیمات عمومی</button>
            </li>
            <li class="nav-item">
                <button class="nav-link {{ $activeTab === 'seo' ? 'active' : '' }}"
                    wire:click="switchTab('seo')">سئو</button>
            </li>
            <li class="nav-item">
                <button class="nav-link {{ $activeTab === 'payment' ? 'active' : '' }}"
                    wire:click="switchTab('payment')">درگاه‌های پرداخت</button>
            </li>
            <li class="nav-item">
                <button class="nav-link {{ $activeTab === 'sms' ? 'active' : '' }}" wire:click="switchTab('sms')">پنل
                    پیامک</button>
            </li>
            <li class="nav-item">
                <button class="nav-link {{ $activeTab === 'callmee' ? 'active' : '' }}"
                    wire:click="switchTab('callmee')">تنظیمات کال می</button>
            </li>
            <li class="nav-item">
                <button class="nav-link {{ $activeTab === 'program' ? 'active' : '' }}"
                    wire:click="switchTab('program')">تنظیمات برنامه</button>
            </li>
            <li class="nav-item">
                <button class="nav-link {{ $activeTab === 'user' ? 'active' : '' }}"
                    wire:click="switchTab('user')">کاربران</button>
            </li>
            <li class="nav-item">
                <button class="nav-link {{ $activeTab === 'security' ? 'active' : '' }}"
                    wire:click="switchTab('security')">تنظیمات امنیتی</button>
            </li>
            <li class="nav-item">
                <button class="nav-link {{ $activeTab === 'files' ? 'active' : '' }}"
                    wire:click="switchTab('files')">تنظیمات فایل‌ها</button>
            </li>
            <li class="nav-item">
                <button class="nav-link {{ $activeTab === 'mail' ? 'active' : '' }}"
                    wire:click="switchTab('mail')">تنظیمات ایمیل</button>
            </li>
            <li class="nav-item">
                <button class="nav-link {{ $activeTab === 'contact' ? 'active' : '' }}"
                    wire:click="switchTab('contact')">اطلاعات تماس</button>
            </li>
            <!-- لینک تغییر لوگو -->
            <li class="nav-item ms-auto">
                <a href="{{ route('admin.Dashboard.setting.change-logo') }}" class="nav-link bg-info text-white">
                    <i class="fas fa-image me-2"></i> تغییر لوگو
                </a>
            </li>
        </ul>

        <!-- محتوای تب‌ها -->
        <div class="tab-content">
            @if (empty($settings))
                <p class="text-danger text-center">هیچ تنظیمی یافت نشد!</p>
            @else
                @foreach ($settings as $group => $groupSettings)
                    <div class="{{ $activeTab === $group ? 'd-block' : 'd-none' }}" id="{{ $group }}-tab">
                        <div class="row g-4">
                            @if (empty($groupSettings))
                                <p class="text-warning text-center">هیچ تنظیمی برای گروه {{ $group }} یافت نشد!</p>
                            @else
                                @foreach ($groupSettings as $index => $setting)
                                    <div class="col-md-6">
                                        <div class="p-3 border rounded bg-light">
                                            <label class="form-label fw-bold text-dark mb-2">{{ $setting['description'] }}</label>
                                            @if ($setting['type'] === 'boolean')
                                                <div class="form-check form-switch">
                                                    <input class="form-check-input" type="checkbox"
                                                        wire:model.live="settings.{{ $group }}.{{ $index }}.value"
                                                        @checked($setting['value'] == 1)>
                                                    <label
                                                        class="form-check-label">{{ $setting['value'] == 1 ? 'فعال' : 'غیرفعال' }}</label>
                                                </div>
                                            @elseif ($setting['type'] === 'string' && in_array($setting['key'], ['type_payment_system', 'theme', 'admintheme', 'extra_login', 'ip_control', 'auth_metod', 'log_threshold', 'o_seite', 'image_align', 'mail_metod', 'smtp_secure', 'allow_cache']))
                                                <select class="form-select" wire:model.live="settings.{{ $group }}.{{ $index }}.value">
                                                    @if ($setting['key'] === 'type_payment_system')
                                                        <option value="membershipfee">پرداخت حق عضویت</option>
                                                        <option value="onlinepayment">پرداخت آنلاین</option>
                                                    @elseif ($setting['key'] === 'theme')
                                                        <option value="portal-old">portal-old</option>
                                                        <option value="portal">portal</option>
                                                    @elseif ($setting['key'] === 'admintheme')
                                                        <option value="nopardaz">nopardaz</option>
                                                    @elseif ($setting['key'] === 'extra_login')
                                                        <option value="0">مداوم</option>
                                                        <option value="1">پایدار</option>
                                                    @elseif ($setting['key'] === 'ip_control')
                                                        <option value="0">عادی</option>
                                                        <option value="1">متوسط</option>
                                                        <option value="2">پیشرفته</option>
                                                    @elseif ($setting['key'] === 'auth_metod')
                                                        <option value="0">نام کاربری</option>
                                                        <option value="1">پست الکترونیکی</option>
                                                    @elseif ($setting['key'] === 'log_threshold')
                                                        <option value="0">غیرفعال</option>
                                                        <option value="1">Error</option>
                                                        <option value="2">Debug</option>
                                                        <option value="3">INFO</option>
                                                        <option value="4">All</option>
                                                    @elseif ($setting['key'] === 'o_seite')
                                                        <option value="0">به‌صورت کامل</option>
                                                        <option value="1">طول</option>
                                                        <option value="2">عرض</option>
                                                    @elseif ($setting['key'] === 'image_align')
                                                        <option value="">هیچ‌کدام</option>
                                                        <option value="left">سمت چپ</option>
                                                        <option value="center">وسط</option>
                                                        <option value="right">سمت راست</option>
                                                    @elseif ($setting['key'] === 'mail_metod')
                                                        <option value="php">PHP Mail()</option>
                                                        <option value="smtp">SMTP</option>
                                                    @elseif ($setting['key'] === 'smtp_secure')
                                                        <option value="">هیچ‌کدام</option>
                                                        <option value="ssl">SSL</option>
                                                        <option value="tls">TLS</option>
                                                    @elseif ($setting['key'] === 'allow_cache')
                                                        <option value="yes">بلی</option>
                                                        <option value="no">خیر</option>
                                                    @endif
                                                </select>
                                            @elseif ($setting['key'] === 'register_default_usergroup')
                                                <select class="form-select" wire:model.live="settings.{{ $group }}.{{ $index }}.value">
                                                    <option value="1">مدیران</option>
                                                    <option value="2">کاربران</option>
                                                    <option value="3">پزشکان</option>
                                                    <option value="4">بیمارستان</option>
                                                    <option value="5">منشی</option>
                                                    <option value="6">منشی درمانگاه</option>
                                                    <option value="7">نمایندگان</option>
                                                </select>
                                            @elseif ($setting['type'] === 'integer' || $setting['type'] === 'string')
                                                <input type="text" class="form-control"
                                                    wire:model.live="settings.{{ $group }}.{{ $index }}.value"
                                                    style="direction: {{ in_array($setting['key'], ['smtp_host', 'smtp_port', 'smtp_user', 'smtp_pass', 'smtp_mail', 'recaptcha_site_key', 'recaptcha_secret_key']) ? 'ltr' : 'rtl' }};">
                                            @endif
                                            <small class="text-muted mt-2 d-block">{{ $setting['description'] }}</small>
                                        </div>
                                    </div>
                                @endforeach
                            @endif
                        </div>
                    </div>
                @endforeach
            @endif
        </div>

        <!-- دکمه ذخیره -->
        <div class="mt-4 text-end">
            <button wire:click="saveSettings" class="btn btn-primary">
                <i class="fa fa-save me-2"></i> ذخیره تغییرات
            </button>
        </div>
    </div>

    <!-- استایل‌ها -->
    <style>
        .bg-primary {
            background: linear-gradient(90deg, #007bff, #0056b3);
        }

        .nav-tabs {
            background: #f8f9fa;
            padding: 0.5rem;
            border-radius: 0.5rem;
        }

        .nav-tabs .nav-link {
            padding: 0.75rem 1.5rem;
            color: #495057;
            background: #fff;
            border: 1px solid #dee2e6;
            margin-right: 0.25rem;
            border-radius: 0.25rem;
            transition: all 0.3s ease;
        }

        .nav-tabs .nav-link.active {
            background: #007bff;
            color: white;
            border-color: #007bff;
        }

        .nav-tabs .nav-link:hover {
            background: #e9ecef;
            border-color: #ced4da;
        }

        .form-control,
        .form-select {
            border-radius: 0.25rem;
            padding: 0.5rem 1rem;
            border: 1px solid #ced4da;
        }

        .form-control:focus,
        .form-select:focus {
            border-color: #007bff;
            box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25);
        }

        .bg-light {
            background-color: #f8f9fa !important;
        }

        .border {
            border-color: #dee2e6 !important;
        }
    </style>
    <script>
        document.addEventListener('livewire:initialized', () => {
            Livewire.on('toast', (message, options = {}) => {
                if (typeof toastr === 'undefined') {
                    console.error('Toastr is not loaded!');
                    return;
                }
                const type = options.type || 'info';
                if (type === 'success') {
                    toastr.success(message, '', {
                        positionClass: options.position || 'toast-top-right',
                        timeOut: options.timeOut || 3000,
                        progressBar: options.progressBar || false,
                    });
                } else if (type === 'error') {
                    toastr.error(message, '', {
                        positionClass: options.position || 'toast-top-right',
                        timeOut: options.timeOut || 3000,
                        progressBar: options.progressBar || false,
                    });
                } else if (type === 'warning') {
                    toastr.warning(message, '', {
                        positionClass: options.position || 'toast-top-right',
                        timeOut: options.timeOut || 3000,
                        progressBar: options.progressBar || false,
                    });
                } else {
                    toastr.info(message, '', {
                        positionClass: options.position || 'toast-top-right',
                        timeOut: options.timeOut || 3000,
                        progressBar: options.progressBar || false,
                    });
                }
            });
        });
    </script>
</div>