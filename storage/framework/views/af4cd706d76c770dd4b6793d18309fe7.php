<div class="container-fluid py-5">
    <!-- هدر اصلی -->
    <div class="bg-gradient-primary text-white p-4 rounded-top-3 shadow-sm">
        <div class="d-flex align-items-center gap-3">
            <i class="fas fa-cog fs-5"></i>
            <h5 class="mb-0 fw-semibold">تنظیمات سامانه</h5>
        </div>
    </div>

    <!-- بدنه اصلی -->
    <div class="bg-white p-5 rounded-bottom-3 shadow-sm">
        <!-- تب‌ها -->
        <div class="tab-wrapper mb-5">
            <ul class="nav nav-tabs-custom justify-content-start flex-wrap gap-2 d-md-flex d-none" role="tablist">
                <li class="nav-item">
                    <button class="nav-link <?php echo e($activeTab === 'general' ? 'active' : ''); ?> px-4 py-2"
                        wire:click="switchTab('general')">تنظیمات عمومی</button>
                </li>
                <li class="nav-item">
                    <button class="nav-link <?php echo e($activeTab === 'seo' ? 'active' : ''); ?> px-4 py-2"
                        wire:click="switchTab('seo')">سئو</button>
                </li>
                <li class="nav-item">
                    <button class="nav-link <?php echo e($activeTab === 'payment' ? 'active' : ''); ?> px-4 py-2"
                        wire:click="switchTab('payment')">درگاه‌های پرداخت</button>
                </li>
                <li class="nav-item">
                    <button class="nav-link <?php echo e($activeTab === 'communication' ? 'active' : ''); ?> px-4 py-2"
                        wire:click="switchTab('communication')">ارتباطات</button>
                </li>
                <li class="nav-item">
                    <button class="nav-link <?php echo e($activeTab === 'callmee' ? 'active' : ''); ?> px-4 py-2"
                        wire:click="switchTab('callmee')">تنظیمات کال می</button>
                </li>
                <li class="nav-item">
                    <button class="nav-link <?php echo e($activeTab === 'program' ? 'active' : ''); ?> px-4 py-2"
                        wire:click="switchTab('program')">تنظیمات برنامه</button>
                </li>
                <li class="nav-item">
                    <button class="nav-link <?php echo e($activeTab === 'security_users' ? 'active' : ''); ?> px-4 py-2"
                        wire:click="switchTab('security_users')">امنیت و کاربران</button>
                </li>
                <li class="nav-item">
                    <button class="nav-link <?php echo e($activeTab === 'files' ? 'active' : ''); ?> px-4 py-2"
                        wire:click="switchTab('files')">تنظیمات فایل‌ها</button>
                </li>
            </ul>

            <!-- تب‌ها برای موبایل -->
            <div class="d-md-none">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <div class="nav-mobile d-flex flex-nowrap overflow-auto gap-2">
                        <button class="nav-link <?php echo e($activeTab === 'general' ? 'active' : ''); ?> px-4 py-2"
                            wire:click="switchTab('general')">تنظیمات عمومی</button>
                        <button class="nav-link <?php echo e($activeTab === 'seo' ? 'active' : ''); ?> px-4 py-2"
                            wire:click="switchTab('seo')">سئو</button>
                        <button class="nav-link <?php echo e($activeTab === 'payment' ? 'active' : ''); ?> px-4 py-2"
                            wire:click="switchTab('payment')">درگاه‌های پرداخت</button>
                    </div>
                    <div class="dropdown">
                        <button class="btn btn-outline-primary dropdown-toggle px-3 py-2 rounded-pill" type="button"
                            data-bs-toggle="dropdown" aria-expanded="false">
                            بیشتر
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li><button class="dropdown-item <?php echo e($activeTab === 'communication' ? 'active' : ''); ?>"
                                    wire:click="switchTab('communication')">ارتباطات</button></li>
                            <li><button class="dropdown-item <?php echo e($activeTab === 'callmee' ? 'active' : ''); ?>"
                                    wire:click="switchTab('callmee')">تنظیمات کال می</button></li>
                            <li><button class="dropdown-item <?php echo e($activeTab === 'program' ? 'active' : ''); ?>"
                                    wire:click="switchTab('program')">تنظیمات برنامه</button></li>
                            <li><button class="dropdown-item <?php echo e($activeTab === 'security_users' ? 'active' : ''); ?>"
                                    wire:click="switchTab('security_users')">امنیت و کاربران</button></li>
                            <li><button class="dropdown-item <?php echo e($activeTab === 'files' ? 'active' : ''); ?>"
                                    wire:click="switchTab('files')">تنظیمات فایل‌ها</button></li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>

        <!-- محتوای تب‌ها -->
        <div class="tab-content">
            <!--[if BLOCK]><![endif]--><?php if(empty($settings)): ?>
                <p class="text-danger text-center py-4">هیچ تنظیمی یافت نشد!</p>
            <?php else: ?>
                <!--[if BLOCK]><![endif]--><?php $__currentLoopData = $settings; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $group => $groupSettings): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <!-- تب‌های اصلی که تو $settings هستن -->
                    <!--[if BLOCK]><![endif]--><?php if(in_array($group, ['general', 'seo', 'payment', 'callmee', 'program', 'files'])): ?>
                        <div class="<?php echo e($activeTab === $group ? 'd-block' : 'd-none'); ?>" id="<?php echo e($group); ?>-tab">
                            <div class="row g-4">
                                <!--[if BLOCK]><![endif]--><?php if(empty($groupSettings)): ?>
                                    <p class="text-warning text-center py-4">هیچ تنظیمی برای گروه <?php echo e($group); ?> یافت نشد!</p>
                                <?php else: ?>
                                    <!--[if BLOCK]><![endif]--><?php $__currentLoopData = $groupSettings; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $setting): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <div class="col-md-6">
                                            <div class="p-4 border rounded-3 bg-light shadow-sm">
                                                <label class="form-label fw-semibold text-dark mb-2"><?php echo e($setting['description']); ?></label>
                                                <!--[if BLOCK]><![endif]--><?php if($setting['type'] === 'boolean'): ?>
                                                    <div class="form-check form-switch">
                                                        <input class="form-check-input" type="checkbox"
                                                            wire:model.live="settings.<?php echo e($group); ?>.<?php echo e($index); ?>.value"
                                                            <?php if($setting['value'] == 1): echo 'checked'; endif; ?>>
                                                        <label class="form-check-label text-muted">
                                                            <?php echo e($setting['value'] == 1 ? 'فعال' : 'غیرفعال'); ?>

                                                        </label>
                                                    </div>
                                                <?php elseif($setting['type'] === 'string' && in_array($setting['key'], ['type_payment_system', 'theme', 'admintheme', 'extra_login', 'ip_control', 'auth_metod', 'log_threshold', 'o_seite', 'image_align', 'mail_metod', 'smtp_secure', 'allow_cache'])): ?>
                                                    <select class="form-select shadow-sm"
                                                        wire:model.live="settings.<?php echo e($group); ?>.<?php echo e($index); ?>.value">
                                                        <!--[if BLOCK]><![endif]--><?php if($setting['key'] === 'type_payment_system'): ?>
                                                            <option value="membershipfee">پرداخت حق عضویت</option>
                                                            <option value="onlinepayment">پرداخت آنلاین</option>
                                                        <?php elseif($setting['key'] === 'theme'): ?>
                                                            <option value="portal-old">portal-old</option>
                                                            <option value="portal">portal</option>
                                                        <?php elseif($setting['key'] === 'admintheme'): ?>
                                                            <option value="nopardaz">nopardaz</option>
                                                        <?php elseif($setting['key'] === 'extra_login'): ?>
                                                            <option value="0">مداوم</option>
                                                            <option value="1">پایدار</option>
                                                        <?php elseif($setting['key'] === 'ip_control'): ?>
                                                            <option value="0">عادی</option>
                                                            <option value="1">متوسط</option>
                                                            <option value="2">پیشرفته</option>
                                                        <?php elseif($setting['key'] === 'auth_metod'): ?>
                                                            <option value="0">نام کاربری</option>
                                                            <option value="1">پست الکترونیکی</option>
                                                        <?php elseif($setting['key'] === 'log_threshold'): ?>
                                                            <option value="0">غیرفعال</option>
                                                            <option value="1">Error</option>
                                                            <option value="2">Debug</option>
                                                            <option value="3">INFO</option>
                                                            <option value="4">All</option>
                                                        <?php elseif($setting['key'] === 'o_seite'): ?>
                                                            <option value="0">به‌صورت کامل</option>
                                                            <option value="1">طول</option>
                                                            <option value="2">عرض</option>
                                                        <?php elseif($setting['key'] === 'image_align'): ?>
                                                            <option value="">هیچ‌کدام</option>
                                                            <option value="left">سمت چپ</option>
                                                            <option value="center">وسط</option>
                                                            <option value="right">سمت راست</option>
                                                        <?php elseif($setting['key'] === 'mail_metod'): ?>
                                                            <option value="php">PHP Mail()</option>
                                                            <option value="smtp">SMTP</option>
                                                        <?php elseif($setting['key'] === 'smtp_secure'): ?>
                                                            <option value="">هیچ‌کدام</option>
                                                            <option value="ssl">SSL</option>
                                                            <option value="tls">TLS</option>
                                                        <?php elseif($setting['key'] === 'allow_cache'): ?>
                                                            <option value="yes">بلی</option>
                                                            <option value="no">خیر</option>
                                                        <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                                                    </select>
                                                <?php elseif($setting['key'] === 'register_default_usergroup'): ?>
                                                    <select class="form-select shadow-sm"
                                                        wire:model.live="settings.<?php echo e($group); ?>.<?php echo e($index); ?>.value">
                                                        <option value="1">مدیران</option>
                                                        <option value="2">کاربران</option>
                                                        <option value="3">پزشکان</option>
                                                        <option value="4">بیمارستان</option>
                                                        <option value="5">منشی</option>
                                                        <option value="6">منشی درمانگاه</option>
                                                        <option value="7">نمایندگان</option>
                                                    </select>
                                                <?php elseif($setting['type'] === 'integer' || $setting['type'] === 'string'): ?>
                                                    <input type="text" class="form-control shadow-sm"
                                                        wire:model.live="settings.<?php echo e($group); ?>.<?php echo e($index); ?>.value"
                                                        style="direction: <?php echo e(in_array($setting['key'], ['smtp_host', 'smtp_port', 'smtp_user', 'smtp_pass', 'smtp_mail', 'recaptcha_site_key', 'recaptcha_secret_key']) ? 'ltr' : 'rtl'); ?>;">
                                                <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                                                <small class="text-muted mt-2 d-block"><?php echo e($setting['description']); ?></small>
                                                <!-- اضافه کردن تغییر لوگو به تنظیمات عمومی -->
                                                <!--[if BLOCK]><![endif]--><?php if($group === 'general' && $index === array_key_last($groupSettings)): ?>
                                                    <div class="mt-3">
                                                        <a href="<?php echo e(route('admin.Dashboard.setting.change-logo')); ?>"
                                                            class="btn btn-outline-success px-4 py-2 rounded-pill d-flex align-items-center gap-2">
                                                            <i class="fas fa-image"></i> تغییر لوگو
                                                        </a>
                                                    </div>
                                                <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                                            </div>
                                        </div>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><!--[if ENDBLOCK]><![endif]-->
                                <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                            </div>
                        </div>
                    <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><!--[if ENDBLOCK]><![endif]-->

                <!-- تب ارتباطات (ادغام sms و mail) -->
                <div class="<?php echo e($activeTab === 'communication' ? 'd-block' : 'd-none'); ?>" id="communication-tab">
                    <div class="row g-4">
                        <!--[if BLOCK]><![endif]--><?php if(!empty($settings['sms'])): ?>
                            <!--[if BLOCK]><![endif]--><?php $__currentLoopData = $settings['sms']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $setting): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <div class="col-md-6">
                                    <div class="p-4 border rounded-3 bg-light shadow-sm">
                                        <label class="form-label fw-semibold text-dark mb-2"><?php echo e($setting['description']); ?></label>
                                        <!--[if BLOCK]><![endif]--><?php if($setting['type'] === 'boolean'): ?>
                                            <div class="form-check form-switch">
                                                <input class="form-check-input" type="checkbox"
                                                    wire:model.live="settings.sms.<?php echo e($index); ?>.value" <?php if($setting['value'] == 1): echo 'checked'; endif; ?>>
                                                <label class="form-check-label text-muted">
                                                    <?php echo e($setting['value'] == 1 ? 'فعال' : 'غیرفعال'); ?>

                                                </label>
                                            </div>
                                        <?php elseif($setting['type'] === 'string' || $setting['type'] === 'integer'): ?>
                                            <input type="text" class="form-control shadow-sm"
                                                wire:model.live="settings.sms.<?php echo e($index); ?>.value"
                                                style="direction: <?php echo e(in_array($setting['key'], ['smtp_host', 'smtp_port', 'smtp_user', 'smtp_pass', 'smtp_mail']) ? 'ltr' : 'rtl'); ?>;">
                                        <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                                        <small class="text-muted mt-2 d-block"><?php echo e($setting['description']); ?></small>
                                    </div>
                                </div>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><!--[if ENDBLOCK]><![endif]-->
                        <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                        <!--[if BLOCK]><![endif]--><?php if(!empty($settings['mail'])): ?>
                            <!--[if BLOCK]><![endif]--><?php $__currentLoopData = $settings['mail']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $setting): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <div class="col-md-6">
                                    <div class="p-4 border rounded-3 bg-light shadow-sm">
                                        <label class="form-label fw-semibold text-dark mb-2"><?php echo e($setting['description']); ?></label>
                                        <!--[if BLOCK]><![endif]--><?php if($setting['type'] === 'boolean'): ?>
                                            <div class="form-check form-switch">
                                                <input class="form-check-input" type="checkbox"
                                                    wire:model.live="settings.mail.<?php echo e($index); ?>.value" <?php if($setting['value'] == 1): echo 'checked'; endif; ?>>
                                                <label class="form-check-label text-muted">
                                                    <?php echo e($setting['value'] == 1 ? 'فعال' : 'غیرفعال'); ?>

                                                </label>
                                            </div>
                                        <?php elseif($setting['type'] === 'string' && in_array($setting['key'], ['mail_metod', 'smtp_secure'])): ?>
                                            <select class="form-select shadow-sm" wire:model.live="settings.mail.<?php echo e($index); ?>.value">
                                                <!--[if BLOCK]><![endif]--><?php if($setting['key'] === 'mail_metod'): ?>
                                                    <option value="php">PHP Mail()</option>
                                                    <option value="smtp">SMTP</option>
                                                <?php elseif($setting['key'] === 'smtp_secure'): ?>
                                                    <option value="">هیچ‌کدام</option>
                                                    <option value="ssl">SSL</option>
                                                    <option value="tls">TLS</option>
                                                <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                                            </select>
                                        <?php elseif($setting['type'] === 'string' || $setting['type'] === 'integer'): ?>
                                            <input type="text" class="form-control shadow-sm"
                                                wire:model.live="settings.mail.<?php echo e($index); ?>.value"
                                                style="direction: <?php echo e(in_array($setting['key'], ['smtp_host', 'smtp_port', 'smtp_user', 'smtp_pass', 'smtp_mail']) ? 'ltr' : 'rtl'); ?>;">
                                        <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                                        <small class="text-muted mt-2 d-block"><?php echo e($setting['description']); ?></small>
                                    </div>
                                </div>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><!--[if ENDBLOCK]><![endif]-->
                        <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                        <!--[if BLOCK]><![endif]--><?php if(empty($settings['sms']) && empty($settings['mail'])): ?>
                            <p class="text-warning text-center py-4">هیچ تنظیمی برای ارتباطات یافت نشد!</p>
                        <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                    </div>
                </div>

                <!-- تب امنیت و کاربران (ادغام security و user) -->
                <div class="<?php echo e($activeTab === 'security_users' ? 'd-block' : 'd-none'); ?>" id="security_users-tab">
                    <div class="row g-4">
                        <!--[if BLOCK]><![endif]--><?php if(!empty($settings['security'])): ?>
                            <!--[if BLOCK]><![endif]--><?php $__currentLoopData = $settings['security']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $setting): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <div class="col-md-6">
                                    <div class="p-4 border rounded-3 bg-light shadow-sm">
                                        <label class="form-label fw-semibold text-dark mb-2"><?php echo e($setting['description']); ?></label>
                                        <!--[if BLOCK]><![endif]--><?php if($setting['type'] === 'boolean'): ?>
                                            <div class="form-check form-switch">
                                                <input class="form-check-input" type="checkbox"
                                                    wire:model.live="settings.security.<?php echo e($index); ?>.value"
                                                    <?php if($setting['value'] == 1): echo 'checked'; endif; ?>>
                                                <label class="form-check-label text-muted">
                                                    <?php echo e($setting['value'] == 1 ? 'فعال' : 'غیرفعال'); ?>

                                                </label>
                                            </div>
                                        <?php elseif($setting['type'] === 'string' && in_array($setting['key'], ['ip_control', 'auth_metod', 'log_threshold'])): ?>
                                            <select class="form-select shadow-sm"
                                                wire:model.live="settings.security.<?php echo e($index); ?>.value">
                                                <!--[if BLOCK]><![endif]--><?php if($setting['key'] === 'ip_control'): ?>
                                                    <option value="0">عادی</option>
                                                    <option value="1">متوسط</option>
                                                    <option value="2">پیشرفته</option>
                                                <?php elseif($setting['key'] === 'auth_metod'): ?>
                                                    <option value="0">نام کاربری</option>
                                                    <option value="1">پست الکترونیکی</option>
                                                <?php elseif($setting['key'] === 'log_threshold'): ?>
                                                    <option value="0">غیرفعال</option>
                                                    <option value="1">Error</option>
                                                    <option value="2">Debug</option>
                                                    <option value="3">INFO</option>
                                                    <option value="4">All</option>
                                                <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                                            </select>
                                        <?php elseif($setting['type'] === 'string' || $setting['type'] === 'integer'): ?>
                                            <input type="text" class="form-control shadow-sm"
                                                wire:model.live="settings.security.<?php echo e($index); ?>.value"
                                                style="direction: <?php echo e(in_array($setting['key'], ['recaptcha_site_key', 'recaptcha_secret_key']) ? 'ltr' : 'rtl'); ?>;">
                                        <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                                        <small class="text-muted mt-2 d-block"><?php echo e($setting['description']); ?></small>
                                    </div>
                                </div>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><!--[if ENDBLOCK]><![endif]-->
                        <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                        <!--[if BLOCK]><![endif]--><?php if(!empty($settings['user'])): ?>
                            <!--[if BLOCK]><![endif]--><?php $__currentLoopData = $settings['user']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $setting): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <div class="col-md-6">
                                    <div class="p-4 border rounded-3 bg-light shadow-sm">
                                        <label class="form-label fw-semibold text-dark mb-2"><?php echo e($setting['description']); ?></label>
                                        <!--[if BLOCK]><![endif]--><?php if($setting['key'] === 'register_default_usergroup'): ?>
                                            <select class="form-select shadow-sm" wire:model.live="settings.user.<?php echo e($index); ?>.value">
                                                <option value="1">مدیران</option>
                                                <option value="2">کاربران</option>
                                                <option value="3">پزشکان</option>
                                                <option value="4">بیمارستان</option>
                                                <option value="5">منشی</option>
                                                <option value="6">منشی درمانگاه</option>
                                                <option value="7">نمایندگان</option>
                                            </select>
                                        <?php elseif($setting['type'] === 'string' || $setting['type'] === 'integer'): ?>
                                            <input type="text" class="form-control shadow-sm"
                                                wire:model.live="settings.user.<?php echo e($index); ?>.value" style="direction: rtl;">
                                        <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                                        <small class="text-muted mt-2 d-block"><?php echo e($setting['description']); ?></small>
                                    </div>
                                </div>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><!--[if ENDBLOCK]><![endif]-->
                        <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                        <!--[if BLOCK]><![endif]--><?php if(empty($settings['security']) && empty($settings['user'])): ?>
                            <p class="text-warning text-center py-4">هیچ تنظیمی برای امنیت و کاربران یافت نشد!</p>
                        <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                    </div>
                </div>
            <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
        </div>

        <!-- دکمه ذخیره -->
        <div class="mt-5 text-end">
            <button wire:click="saveSettings"
                class="btn btn-primary px-4 py-2 rounded-pill d-flex align-items-center gap-2">
                <i class="fa fa-save"></i> ذخیره تغییرات
            </button>
        </div>
    </div>

    <!-- استایل‌ها -->
    <style>
        .bg-gradient-primary {
            background: linear-gradient(90deg, #4f46e5, #7c3aed);
        }

        .tab-wrapper {
            position: relative;
        }

        .nav-tabs-custom .nav-link {
            background: #f1f5f9;
            color: #374151;
            font-weight: 500;
            border-radius: 0.75rem;
            padding: 0.75rem 1.5rem;
            border: none;
            transition: all 0.3s ease;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
        }

        .nav-tabs-custom .nav-link:hover {
            background: #e5e7eb;
            color: #1f2937;
        }

        .nav-tabs-custom .nav-link.active {
            background: #4f46e5;
            color: white;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        .nav-mobile {
            white-space: nowrap;
            -ms-overflow-style: none;
            scrollbar-width: none;
        }

        .nav-mobile::-webkit-scrollbar {
            display: none;
        }

        .nav-mobile .nav-link {
            background: #f1f5f9;
            color: #374151;
            font-weight: 500;
            border-radius: 0.75rem;
            padding: 0.75rem 1.5rem;
            border: none;
            transition: all 0.3s ease;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
        }

        .nav-mobile .nav-link:hover {
            background: #e5e7eb;
            color: #1f2937;
        }

        .nav-mobile .nav-link.active {
            background: #4f46e5;
            color: white;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        .dropdown-menu {
            border-radius: 0.75rem;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        .dropdown-item {
            padding: 0.5rem 1.5rem;
            color: #374151;
        }

        .dropdown-item:hover {
            background-color: #f3f4f6;
            color: #4f46e5;
        }

        .dropdown-item.active {
            background-color: #4f46e5;
            color: white;
        }

        .form-control,
        .form-select {
            border-radius: 0.5rem;
            padding: 0.75rem 1rem;
            border: 1px solid #d1d5db;
            transition: all 0.2s ease;
        }

        .form-control:focus,
        .form-select:focus {
            border-color: #4f46e5;
            box-shadow: 0 0 0 0.2rem rgba(79, 70, 229, 0.25);
        }

        .form-check-input:checked {
            background-color: #4f46e5;
            border-color: #4f46e5;
        }

        .bg-light {
            background-color: #f9fafb !important;
        }

        .rounded-top-3 {
            border-top-left-radius: 1rem;
            border-top-right-radius: 1rem;
        }

        .rounded-bottom-3 {
            border-bottom-left-radius: 1rem;
            border-bottom-right-radius: 1rem;
        }

        @media (max-width: 767px) {
            .nav-mobile {
                flex-wrap: nowrap;
                justify-content: flex-start;
            }
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
                const toastOptions = {
                    positionClass: options.position || 'toast-top-right',
                    timeOut: options.timeOut || 3000,
                    progressBar: options.progressBar || false,
                };
                if (type === 'success') toastr.success(message, '', toastOptions);
                else if (type === 'error') toastr.error(message, '', toastOptions);
                else if (type === 'warning') toastr.warning(message, '', toastOptions);
                else toastr.info(message, '', toastOptions);
            });
        });
    </script>
</div><?php /**PATH D:\MyProjects\Benobe\panel\resources\views/livewire/admin/dashboard/system-setting/settings-component.blade.php ENDPATH**/ ?>