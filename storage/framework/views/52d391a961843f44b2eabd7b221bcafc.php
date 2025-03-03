<div class="container-fluid py-1">
    <header class="glass-header p-3 rounded-3 mb-2 shadow-lg">
        <h4 class="mb-0 fw-bold text-white">ویرایش بیمارستان</h4>
    </header>

    <div class="card shadow-sm">
        <div class="card-body p-3">
            <form wire:submit.prevent="save">
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="control-label fw-bold mb-1 fs-6">نام مسئول (پزشک)</label>
                        <div wire:ignore>
                            <select id="doctor-select"
                                class="form-control select2 <?php $__errorArgs = ['doctor_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>">
                                <option value="">انتخاب کنید...</option>
                                <!--[if BLOCK]><![endif]--><?php $__currentLoopData = $doctors; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $doctor): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value="<?php echo e($doctor->id); ?>"><?php echo e($doctor->first_name . ' ' . $doctor->last_name); ?>

                                    </option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><!--[if ENDBLOCK]><![endif]-->
                            </select>
                        </div>
                        <!--[if BLOCK]><![endif]--><?php $__errorArgs = ['doctor_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                            <span class="text-danger"><?php echo e($message); ?></span>
                        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><!--[if ENDBLOCK]><![endif]-->
                    </div>
                    <div class="col-md-6">
                        <label class="control-label fw-bold mb-1 fs-6">نام بیمارستان</label>
                        <input type="text" wire:model="name"
                            class="form-control input-shiny <?php $__errorArgs = ['name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                            placeholder="نام بیمارستان">
                        <!--[if BLOCK]><![endif]--><?php $__errorArgs = ['name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                            <span class="text-danger"><?php echo e($message); ?></span>
                        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><!--[if ENDBLOCK]><![endif]-->
                    </div>
                    <div class="col-md-6">
                        <label class="control-label fw-bold mb-1 fs-6">شماره تماس</label>
                        <input type="text" wire:model="phone_number"
                            class="form-control input-shiny <?php $__errorArgs = ['phone_number'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                            placeholder="شماره تماس">
                        <!--[if BLOCK]><![endif]--><?php $__errorArgs = ['phone_number'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                            <span class="text-danger"><?php echo e($message); ?></span>
                        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><!--[if ENDBLOCK]><![endif]-->
                    </div>
                    <div class="col-md-6">
                        <label class="control-label fw-bold mb-1 fs-6">استان</label>
                        <div wire:ignore>
                            <select id="province-select"
                                class="form-control select2 <?php $__errorArgs = ['province_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>">
                                <option value="">انتخاب کنید...</option>
                                <!--[if BLOCK]><![endif]--><?php $__currentLoopData = $provinces; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $province): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value="<?php echo e($province->id); ?>"><?php echo e($province->name); ?></option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><!--[if ENDBLOCK]><![endif]-->
                            </select>
                        </div>
                        <!--[if BLOCK]><![endif]--><?php $__errorArgs = ['province_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                            <span class="text-danger"><?php echo e($message); ?></span>
                        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><!--[if ENDBLOCK]><![endif]-->
                    </div>
                    <div class="col-md-6">
                        <label class="control-label fw-bold mb-1 fs-6">شهر</label>
                        <div wire:ignore.self>
                            <select id="city-select"
                                class="form-control select2 <?php $__errorArgs = ['city_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>">
                                <option value="">ابتدا استان را انتخاب کنید</option>
                                <!--[if BLOCK]><![endif]--><?php $__currentLoopData = $cities; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $city): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value="<?php echo e($city->id); ?>"><?php echo e($city->name); ?></option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><!--[if ENDBLOCK]><![endif]-->
                            </select>
                        </div>
                        <!--[if BLOCK]><![endif]--><?php $__errorArgs = ['city_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                            <span class="text-danger"><?php echo e($message); ?></span>
                        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><!--[if ENDBLOCK]><![endif]-->
                    </div>
                    <div class="col-md-12">
                        <label class="control-label fw-bold mb-1 fs-6">آدرس</label>
                        <textarea wire:model="address"
                            class="form-control input-shiny <?php $__errorArgs = ['address'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                            placeholder="آدرس بیمارستان"></textarea>
                        <!--[if BLOCK]><![endif]--><?php $__errorArgs = ['address'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                            <span class="text-danger"><?php echo e($message); ?></span>
                        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><!--[if ENDBLOCK]><![endif]-->
                    </div>
                    <div class="col-md-12">
                        <button type="submit" class="btn btn-primary">به‌روزرسانی</button>
                        <a href="<?php echo e(route('admin.content.hospitals.hospitals-management.index')); ?>"
                            class="btn btn-secondary">بازگشت</a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <style>
        .input-shiny {
            border: 1px solid #d1d5db;
            border-radius: 6px;
            padding: 8px;
            font-size: 14px;
            transition: all 0.3s ease;
        }

        .input-shiny:focus {
            border-color: #4f46e5;
            box-shadow: 0 0 0 0.2rem rgba(79, 70, 229, 0.25);
        }

        .is-invalid {
            border-color: #dc3545;
        }

        .text-danger {
            color: #dc3545;
            font-size: 0.875rem;
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
    </style>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // مقداردهی اولیه Select2
            function initializeSelect2() {
                $('#doctor-select').select2({
                    dir: 'rtl',
                    placeholder: 'انتخاب کنید...'
                }).val("<?php echo e($doctor_id ?? ''); ?>").trigger('change');

                $('#province-select').select2({
                    dir: 'rtl',
                    placeholder: 'انتخاب کنید...'
                }).val("<?php echo e($province_id ?? ''); ?>").trigger('change');

                $('#city-select').select2({
                    dir: 'rtl',
                    placeholder: 'انتخاب کنید...'
                }).val("<?php echo e($city_id ?? ''); ?>").trigger('change');
            }

            initializeSelect2();

            // همگام‌سازی با Livewire
            let isUpdating = false;

            $('#doctor-select').on('change', function () {
                if (!isUpdating) {
                    isUpdating = true;
                    window.Livewire.find('<?php echo e($_instance->getId()); ?>').set('doctor_id', $(this).val()).then(() => {
                        isUpdating = false;
                    });
                }
            });

            $('#province-select').on('change', function () {
                if (!isUpdating) {
                    isUpdating = true;
                    const provinceId = $(this).val();
                    window.Livewire.find('<?php echo e($_instance->getId()); ?>').set('province_id', provinceId).then(() => {
                        $('#city-select').select2({
                            dir: 'rtl',
                            placeholder: 'انتخاب کنید...'
                        }).val(null).trigger('change');
                        $('#province-select').select2({
                            dir: 'rtl',
                            placeholder: 'انتخاب کنید...'
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

            // آپدیت Select2 بعد از رندر Livewire
            document.addEventListener('livewire:updated', () => {
                $('#doctor-select').select2({
                    dir: 'rtl',
                    placeholder: 'انتخاب کنید...'
                }).val(window.Livewire.find('<?php echo e($_instance->getId()); ?>').doctor_id).trigger('change');

                $('#province-select').select2({
                    dir: 'rtl',
                    placeholder: 'انتخاب کنید...'
                }).val(window.Livewire.find('<?php echo e($_instance->getId()); ?>').province_id).trigger('change');

                $('#city-select').select2({
                    dir: 'rtl',
                    placeholder: 'انتخاب کنید...'
                }).val(window.Livewire.find('<?php echo e($_instance->getId()); ?>').city_id).trigger('change');
            });

            // نمایش توستر
            Livewire.on('toast', (event) => {
                const data = event[0];
                const { message, type } = data;
                toastr[type === 'success' ? 'success' : 'error'](message, '', {
                    positionClass: 'toast-top-right',
                    timeOut: 3000
                });
            });
        });
    </script>
</div><?php /**PATH D:\MyProjects\Benobe\panel\resources\views/livewire/admin/hospitals/hospitals-management/hospital-edit.blade.php ENDPATH**/ ?>