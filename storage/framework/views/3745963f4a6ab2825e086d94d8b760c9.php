<div>
    <div class="card shadow-sm">
        <div class="card-body p-3">
            <form wire:submit.prevent="update">
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="fw-bold mb-1">موبایل: <span class="text-danger">*</span></label>
                        <input type="text" wire:model="mobile" class="form-control input-shiny"
                            placeholder="موبایل ...">
                        <!--[if BLOCK]><![endif]--><?php $__errorArgs = ['mobile'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <span class="text-danger"><?php echo e($message); ?></span> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><!--[if ENDBLOCK]><![endif]-->
                    </div>
                    <div class="col-md-6">
                        <label class="fw-bold mb-1">نام: <span class="text-danger">*</span></label>
                        <input type="text" wire:model="first_name" class="form-control input-shiny"
                            placeholder="نام خود را وارد کنید">
                        <!--[if BLOCK]><![endif]--><?php $__errorArgs = ['first_name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <span class="text-danger"><?php echo e($message); ?></span> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><!--[if ENDBLOCK]><![endif]-->
                    </div>
                    <div class="col-md-6">
                        <label class="fw-bold mb-1">نام خانوادگی: <span class="text-danger">*</span></label>
                        <input type="text" wire:model="last_name" class="form-control input-shiny"
                            placeholder="نام خانوادگی خود را وارد کنید">
                        <!--[if BLOCK]><![endif]--><?php $__errorArgs = ['last_name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <span class="text-danger"><?php echo e($message); ?></span> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><!--[if ENDBLOCK]><![endif]-->
                    </div>
                    <div class="col-md-6">
                        <label class="fw-bold mb-1">شماره پروانه: <span class="text-danger">*</span></label>
                        <input type="text" wire:model="license_number" class="form-control input-shiny"
                            placeholder="شماره پروانه خود را وارد کنید">
                        <!--[if BLOCK]><![endif]--><?php $__errorArgs = ['license_number'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <span class="text-danger"><?php echo e($message); ?></span> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><!--[if ENDBLOCK]><![endif]-->
                    </div>
                    <div class="col-md-6">
                        <label class="fw-bold mb-1">جنسیت: <span class="text-danger">*</span></label>
                        <div wire:ignore>
                            <select id="sex-select" class="form-control">
                                <option value="male">مرد</option>
                                <option value="female">زن</option>
                                <option value="other">سایر</option>
                            </select>
                        </div>
                        <!--[if BLOCK]><![endif]--><?php $__errorArgs = ['sex'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <span class="text-danger"><?php echo e($message); ?></span> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><!--[if ENDBLOCK]><![endif]-->
                    </div>
                    <div class="col-md-6">
                        <label class="fw-bold mb-1">تصویر پرسنلی:</label>
                        <input type="file" wire:model="avatar" class="form-control input-shiny">
                        <div class="mt-2">
                            <!--[if BLOCK]><![endif]--><?php if($avatar): ?>
                                <img src="<?php echo e($avatar->temporaryUrl()); ?>" style="width: 50px; height: 50px;" class="rounded">
                            <?php elseif($currentAvatar): ?>
                                <img src="<?php echo e(Storage::url($currentAvatar)); ?>" style="width: 50px; height: 50px;"
                                    class="rounded">
                            <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                        </div>
                        <!--[if BLOCK]><![endif]--><?php $__errorArgs = ['avatar'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <span class="text-danger"><?php echo e($message); ?></span> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><!--[if ENDBLOCK]><![endif]-->
                    </div>
                    <div class="col-md-12">
                        <label class="fw-bold mb-1">درباره من: <span class="text-danger">*</span></label>
                        <textarea wire:model="aboutme" class="form-control input-shiny"
                            style="height: 100px;"></textarea>
                        <!--[if BLOCK]><![endif]--><?php $__errorArgs = ['aboutme'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <span class="text-danger"><?php echo e($message); ?></span> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><!--[if ENDBLOCK]><![endif]-->
                    </div>
                    <div class="col-md-12">
                        <label class="fw-bold mb-1">نکات مهم:</label>
                        <textarea wire:model="important_points" class="form-control input-shiny"
                            style="height: 100px;"></textarea>
                        <!--[if BLOCK]><![endif]--><?php $__errorArgs = ['important_points'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <span class="text-danger"><?php echo e($message); ?></span> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><!--[if ENDBLOCK]><![endif]-->
                    </div>
                    <div class="col-md-6">
                        <label class="fw-bold mb-1">تلفن تماس کلینیک:</label>
                        <input type="text" wire:model="clinic_tel" class="form-control input-shiny"
                            placeholder="تلفن تماس">
                        <!--[if BLOCK]><![endif]--><?php $__errorArgs = ['clinic_tel'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <span class="text-danger"><?php echo e($message); ?></span> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><!--[if ENDBLOCK]><![endif]-->
                    </div>
                    <div class="col-md-6">
                        <label class="fw-bold mb-1">آدرس کلینیک: <span class="text-danger">*</span></label>
                        <input type="text" wire:model="clinic_address" class="form-control input-shiny"
                            placeholder="آدرس">
                        <!--[if BLOCK]><![endif]--><?php $__errorArgs = ['clinic_address'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <span class="text-danger"><?php echo e($message); ?></span> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><!--[if ENDBLOCK]><![endif]-->
                    </div>
                    <div class="col-md-6">
                        <label class="fw-bold mb-1">استان کلینیک: <span class="text-danger">*</span></label>
                        <div wire:ignore>
                            <select id="province-select" class="form-control">
                                <option value="">انتخاب استان</option>
                                <!--[if BLOCK]><![endif]--><?php $__currentLoopData = $provinces; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $province): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value="<?php echo e($province->id); ?>"><?php echo e($province->name); ?></option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><!--[if ENDBLOCK]><![endif]-->
                            </select>
                        </div>
                        <!--[if BLOCK]><![endif]--><?php $__errorArgs = ['province_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <span class="text-danger"><?php echo e($message); ?></span> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><!--[if ENDBLOCK]><![endif]-->
                    </div>
                    <div class="col-md-6">
                        <label class="fw-bold mb-1">شهر:</label>
                        <div wire:ignore.self>
                            <select id="city-select" class="form-control">
                                <option value="">-- بدون انتخاب --</option>
                                <!--[if BLOCK]><![endif]--><?php $__currentLoopData = $cities; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $city): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value="<?php echo e($city->id); ?>"><?php echo e($city->name); ?></option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><!--[if ENDBLOCK]><![endif]-->
                            </select>
                        </div>
                        <!--[if BLOCK]><![endif]--><?php $__errorArgs = ['city_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <span class="text-danger"><?php echo e($message); ?></span> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><!--[if ENDBLOCK]><![endif]-->
                    </div>
                    <div class="col-md-12">
                        <label class="fw-bold mb-1">انتخاب تخصص‌ها: <span class="text-danger">*</span></label>
                        <div wire:ignore>
                            <select id="specialties-select" multiple class="form-control">
                                <!--[if BLOCK]><![endif]--><?php $__currentLoopData = $specialtiesList; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $specialty): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value="<?php echo e($specialty->id); ?>"><?php echo e($specialty->name); ?></option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><!--[if ENDBLOCK]><![endif]-->
                            </select>
                        </div>
                        <!--[if BLOCK]><![endif]--><?php $__errorArgs = ['specialties'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <span class="text-danger"><?php echo e($message); ?></span> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><!--[if ENDBLOCK]><![endif]-->
                    </div>
                    <div class="col-md-6">
                        <label class="fw-bold mb-1">حالت Security پیشرفته:</label>
                        <div wire:ignore>
                            <select id="security-select" class="form-control">
                                <option value="0">غیرفعال</option>
                                <option value="1">فعال</option>
                            </select>
                        </div>
                        <!--[if BLOCK]><![endif]--><?php $__errorArgs = ['security'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <span class="text-danger"><?php echo e($message); ?></span> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><!--[if ENDBLOCK]><![endif]-->
                    </div>
                    <div class="col-md-6">
                        <label class="fw-bold mb-1">تعرفه ویزیت آزاد (تومان):</label>
                        <input type="number" wire:model="price_doctor_nobat" class="form-control input-shiny" min="0">
                        <small class="text-muted">چنانچه رایگان است، 0 وارد کنید。</small>
                        <!--[if BLOCK]><![endif]--><?php $__errorArgs = ['price_doctor_nobat'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <span class="text-danger"><?php echo e($message); ?></span> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><!--[if ENDBLOCK]><![endif]-->
                    </div>
                    <div class="col-md-6">
                        <label class="fw-bold mb-1">تعرفه حق نوبت سایت (تومان):</label>
                        <input type="number" wire:model="price_per_nobatsite" class="form-control input-shiny" min="0">
                        <small class="text-muted">چنانچه رایگان است، 0 وارد کنید。</small>
                        <!--[if BLOCK]><![endif]--><?php $__errorArgs = ['price_per_nobatsite'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <span class="text-danger"><?php echo e($message); ?></span> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><!--[if ENDBLOCK]><![endif]-->
                    </div>
                    <div class="col-md-12">
                        <div class="form-check">
                            <input type="checkbox" wire:model="status_moshavere" class="form-check-input"
                                id="status_moshavere">
                            <label class="form-check-label" for="status_moshavere">آیا مشاوره تلفنی فعال باشد؟</label>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="form-check">
                            <input type="checkbox" wire:model="status_nobatdehi" class="form-check-input"
                                id="status_nobatdehi">
                            <label class="form-check-label" for="status_nobatdehi">آیا نوبت‌دهی حضوری فعال باشد؟</label>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="form-check">
                            <input type="checkbox" wire:model="send_sms" class="form-check-input" id="send_sms">
                            <label class="form-check-label" for="send_sms">آیا پیامک تغییرات به پزشک ارسال شود؟</label>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="form-check">
                            <input type="checkbox" wire:model="auth" class="form-check-input" id="auth">
                            <label class="form-check-label" for="auth">احراز هویت انجام شود؟</label>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <label class="fw-bold mb-1">وضعیت: <span class="text-danger">*</span></label>
                        <div wire:ignore>
                            <select id="status-select" class="form-control">
                                <option value="0">مرحله اول ثبت‌نام</option>
                                <option value="1">مرحله انتخاب تخصص</option>
                                <option value="2">مرحله تنظیم برنامه نوبت‌دهی</option>
                                <option value="3">مرحله تنظیم برنامه مشاوره</option>
                                <option value="4">نهایی شده</option>
                            </select>
                        </div>
                        <!--[if BLOCK]><![endif]--><?php $__errorArgs = ['status'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <span class="text-danger"><?php echo e($message); ?></span> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><!--[if ENDBLOCK]><![endif]-->
                    </div>
                    <div class="col-md-12 text-end">
                        <button type="submit" class="btn btn-gradient-primary">ثبت و ذخیره</button>
                        <a href="<?php echo e(route('admin.doctors.doctors-management.index')); ?>"
                            class="btn btn-secondary">بازگشت</a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // مقداردهی اولیه Select2
            function initializeSelect2() {
                $('#province-select').select2({
                    dir: 'rtl',
                    placeholder: 'انتخاب استان',
                    allowClear: true
                }).val("<?php echo e($province_id ?? ''); ?>").trigger('change');

                $('#city-select').select2({
                    dir: 'rtl',
                    placeholder: 'انتخاب شهر',
                    allowClear: true
                }).val("<?php echo e($city_id ?? ''); ?>").trigger('change');

                $('#sex-select').select2({
                    dir: 'rtl',
                    placeholder: 'انتخاب جنسیت',
                    allowClear: true
                }).val("<?php echo e($sex ?? ''); ?>").trigger('change');

                $('#specialties-select').select2({
                    dir: 'rtl',
                    placeholder: 'تخصص‌ها را انتخاب کنید',
                    allowClear: true
                }).val(<?php echo json_encode($specialties ?? [], 15, 512) ?>).trigger('change');

                $('#security-select').select2({
                    dir: 'rtl',
                    placeholder: 'انتخاب حالت',
                    allowClear: true
                }).val("<?php echo e($security ?? ''); ?>").trigger('change');

                $('#status-select').select2({
                    dir: 'rtl',
                    placeholder: 'انتخاب وضعیت',
                    allowClear: true
                }).val("<?php echo e($status ?? ''); ?>").trigger('change');
            }

            // بارگذاری اولیه
            initializeSelect2();

            // همگام‌سازی انتخاب‌ها با Livewire
            let isUpdating = false;

            $('#province-select').on('change', function () {
                if (!isUpdating) {
                    isUpdating = true;
                    const provinceId = $(this).val();
                    window.Livewire.find('<?php echo e($_instance->getId()); ?>').set('province_id', provinceId).then(() => {
                        // بازسازی Select2 برای شهرها و استان بعد از آپدیت
                        $('#city-select').select2({
                            dir: 'rtl',
                            placeholder: 'انتخاب شهر',
                            allowClear: true
                        }).val(null).trigger('change');

                        $('#province-select').select2({
                            dir: 'rtl',
                            placeholder: 'انتخاب استان',
                            allowClear: true
                        }).val(provinceId).trigger('change');

                        isUpdating = false;
                    });
                }
            });

            $('#city-select').on('change', function () {
                if (!isUpdating) {
                    isUpdating = true;
                    window.Livewire.find('<?php echo e($_instance->getId()); ?>').set('city_id', $(this).val()).then(() => {
                        isUpdating = false;
                    });
                }
            });

            $('#sex-select').on('change', function () {
                if (!isUpdating) {
                    isUpdating = true;
                    window.Livewire.find('<?php echo e($_instance->getId()); ?>').set('sex', $(this).val()).then(() => {
                        isUpdating = false;
                    });
                }
            });

            $('#specialties-select').on('change', function () {
                if (!isUpdating) {
                    isUpdating = true;
                    window.Livewire.find('<?php echo e($_instance->getId()); ?>').set('specialties', $(this).val()).then(() => {
                        isUpdating = false;
                    });
                }
            });

            $('#security-select').on('change', function () {
                if (!isUpdating) {
                    isUpdating = true;
                    window.Livewire.find('<?php echo e($_instance->getId()); ?>').set('security', $(this).val()).then(() => {
                        isUpdating = false;
                    });
                }
            });

            $('#status-select').on('change', function () {
                if (!isUpdating) {
                    isUpdating = true;
                    window.Livewire.find('<?php echo e($_instance->getId()); ?>').set('status', $(this).val()).then(() => {
                        isUpdating = false;
                    });
                }
            });

            // نمایش توستر
            Livewire.on('toast', (event) => {
                const data = event[0];
                const { message, type } = data;
                toastr[type || 'info'](message, '', {
                    positionClass: 'toast-top-right',
                    timeOut: 3000,
                    progressBar: false,
                });
            });
        });
    </script>

    <style>
        .glass-header {
            background: linear-gradient(135deg, rgba(79, 70, 229, 0.9), rgba(124, 58, 237, 0.7));
            backdrop-filter: blur(12px);
            border: 1px solid rgba(255, 255, 255, 0.3);
            box-shadow: 0 6px 20px rgba(0, 0, 0, 0.15);
            transition: all 0.3s ease;
        }

        .glass-header:hover {
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.2);
        }

        .input-shiny {
            border: 1px solid #d1d5db;
            border-radius: 6px;
            padding: 8px;
            font-size: 14px;
            transition: all 0.3s ease;
            background: #fff;
            box-shadow: inset 0 1px 3px rgba(0, 0, 0, 0.05);
            height: 40px;
        }

        .input-shiny:focus {
            border-color: #4f46e5;
            box-shadow: 0 0 0 0.2rem rgba(79, 70, 229, 0.25);
        }

        .btn-gradient-primary {
            background: linear-gradient(90deg, #4f46e5, #7c3aed);
            border: none;
            color: white;
        }

        .btn-gradient-primary:hover {
            background: linear-gradient(90deg, #4338ca, #6d28d9);
            transform: translateY(-1px);
        }

        .select2-container {
            width: 100% !important;
        }

        .select2-selection {
            border: 1px solid #d1d5db;
            border-radius: 6px;
            padding: 8px;
            font-size: 14px;
            height: 40px;
            display: flex;
            align-items: center;
        }

        .select2-selection__rendered {
            line-height: 24px !important;
        }

        .select2-selection--multiple {
            height: auto !important;
            min-height: 40px;
        }

        .select2-selection__choice {
            margin-top: 4px !important;
        }
    </style>
</div><?php /**PATH D:\MyProjects\Benobe\panel\resources\views/livewire/admin/doctors/doctors-management-edit.blade.php ENDPATH**/ ?>