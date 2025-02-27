<div class="container-fluid py-4">
    <!-- هدر اصلی -->
    <div class="bg-light text-dark p-4 rounded-top border">
        <div class="d-flex align-items-center">
            <i class="fas fa-user-plus me-3"></i>
            <h5 class="mb-0 fw-bold">افزودن نماینده</h5>
        </div>
    </div>

    <!-- بدنه اصلی -->
    <div class="bg-white p-4 rounded-bottom shadow-sm">
        <form wire:submit.prevent="save">
            <div class="row g-4">
                <!-- نام و نام خانوادگی -->
                <div class="col-md-6">
                    <div class="form-group">
                        <label class="form-label fw-bold">نام و نام خانوادگی</label>
                        <input type="text" class="form-control" wire:model="full_name">
                        <!--[if BLOCK]><![endif]--><?php $__errorArgs = ['full_name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <span class="text-danger d-block mt-1"><?php echo e($message); ?></span> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><!--[if ENDBLOCK]><![endif]-->
                    </div>
                </div>

                <!-- موبایل -->
                <div class="col-md-6">
                    <div class="form-group">
                        <label class="form-label fw-bold">موبایل</label>
                        <input type="text" class="form-control" wire:model="mobile">
                        <!--[if BLOCK]><![endif]--><?php $__errorArgs = ['mobile'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <span class="text-danger d-block mt-1"><?php echo e($message); ?></span> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><!--[if ENDBLOCK]><![endif]-->
                    </div>
                </div>

                <!-- کد ملی -->
                <div class="col-md-6">
                    <div class="form-group">
                        <label class="form-label fw-bold">کد ملی</label>
                        <input type="text" class="form-control" wire:model="national_code">
                        <!--[if BLOCK]><![endif]--><?php $__errorArgs = ['national_code'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <span class="text-danger d-block mt-1"><?php echo e($message); ?></span> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><!--[if ENDBLOCK]><![endif]-->
                    </div>
                </div>

                <!-- استان -->
                <div class="col-md-6">
                    <div class="form-group">
                        <label class="form-label fw-bold">استان</label>
                        <select class="form-control" id="province-select" wire:model.live="province_id">
                            <option value="">انتخاب استان</option>
                            <!--[if BLOCK]><![endif]--><?php $__currentLoopData = $provinces; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $province): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($province['id']); ?>" <?php echo e($province_id == $province['id'] ? 'selected' : ''); ?>>
                                    <?php echo e($province['name']); ?></option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><!--[if ENDBLOCK]><![endif]-->
                        </select>
                        <!--[if BLOCK]><![endif]--><?php $__errorArgs = ['province_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <span class="text-danger d-block mt-1"><?php echo e($message); ?></span> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><!--[if ENDBLOCK]><![endif]-->
                    </div>
                </div>

                <!-- شهر -->
                <div class="col-md-6">
                    <div class="form-group">
                        <label class="form-label fw-bold">شهر</label>
                        <select class="form-control" id="city-select" wire:model="city_id">
                            <option value="">انتخاب شهر</option>
                            <!--[if BLOCK]><![endif]--><?php $__currentLoopData = $cities; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $city): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($city['id']); ?>" <?php echo e($city_id == $city['id'] ? 'selected' : ''); ?>>
                                    <?php echo e($city['name']); ?></option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><!--[if ENDBLOCK]><![endif]-->
                        </select>
                        <!--[if BLOCK]><![endif]--><?php $__errorArgs = ['city_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <span class="text-danger d-block mt-1"><?php echo e($message); ?></span> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><!--[if ENDBLOCK]><![endif]-->
                    </div>
                </div>

                <!-- وضعیت -->
                <div class="col-md-6">
                    <div class="form-group">
                        <label class="form-label fw-bold">وضعیت</label>
                        <div class="form-check form-switch">
                            <input type="checkbox" class="form-check-input" wire:model="status" <?php if($status): echo 'checked'; endif; ?>>
                            <label class="form-check-label"><?php echo e($status ? 'فعال' : 'غیرفعال'); ?></label>
                        </div>
                        <!--[if BLOCK]><![endif]--><?php $__errorArgs = ['status'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <span class="text-danger d-block mt-1"><?php echo e($message); ?></span> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><!--[if ENDBLOCK]><![endif]-->
                    </div>
                </div>
            </div>

            <!-- دکمه‌ها -->
            <div class="d-flex justify-content-between mt-4">
                <a href="<?php echo e(route('admin.agent.agent')); ?>" class="btn btn-outline-warning">
                    <i class="fas fa-arrow-right me-2"></i> بازگشت
                </a>
                <button type="submit" class="btn btn-success">
                    <i class="fas fa-save me-2"></i> ثبت
                </button>
            </div>
        </form>
    </div>

    <!-- استایل‌ها -->
    <style>
        .bg-light {
            background-color: #f8f9fa !important;
        }

        .border {
            border-color: #dee2e6 !important;
        }

        .form-control:focus {
            border-color: #007bff;
            box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25);
        }

        .btn {
            border-radius: 0.375rem;
            padding: 0.75rem 1.5rem;
        }

        .btn-success {
            background-color: #28a745;
            border-color: #28a745;
        }

        .btn-success:hover {
            background-color: #218838;
            border-color: #1e7e34;
        }

        .btn-outline-warning {
            color: #ffc107;
            border-color: #ffc107;
        }

        .btn-outline-warning:hover {
            background-color: #ffc107;
            color: #fff;
        }

        .form-switch .form-check-input {
            width: 2.5em;
            height: 1.25em;
        }
    </style>

    <!-- اسکریپت TomSelect -->
    <script>
        document.addEventListener('livewire:initialized', () => {
            const provinceSelect = new TomSelect('#province-select', {
                create: false,
                sortField: 'text',
                placeholder: 'انتخاب استان',
                valueField: 'value',
                labelField: 'text',
                searchField: ['text'],
                options: [
                    { value: '', text: 'انتخاب استان' },
                    <!--[if BLOCK]><![endif]--><?php $__currentLoopData = $provinces; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $province): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        { value: '<?php echo e($province['id']); ?>', text: '<?php echo e($province['name']); ?>' },
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><!--[if ENDBLOCK]><![endif]-->
                ],
                onChange: function (value) {
                    window.Livewire.find('<?php echo e($_instance->getId()); ?>').set('province_id', value);
                },
                onInitialize: function () {
                    this.setValue('<?php echo e($province_id ?? ''); ?>');
                }
            });

            const citySelect = new TomSelect('#city-select', {
                create: false,
                sortField: 'text',
                placeholder: 'انتخاب شهر',
                valueField: 'value',
                labelField: 'text',
                searchField: ['text'],
                options: [
                    { value: '', text: 'انتخاب شهر' }
                ],
                onChange: function (value) {
                    window.Livewire.find('<?php echo e($_instance->getId()); ?>').set('city_id', value);
                },
                onInitialize: function () {
                    this.setValue('<?php echo e($city_id ?? ''); ?>');
                }
            });

            // آپدیت شهرها
            document.addEventListener('livewire:updated', function () {
                const cities = [
                    { value: '', text: 'انتخاب شهر' },
                    <!--[if BLOCK]><![endif]--><?php $__currentLoopData = $cities; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $city): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        { value: '<?php echo e($city['id']); ?>', text: '<?php echo e($city['name']); ?>' },
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><!--[if ENDBLOCK]><![endif]-->
                ];
                citySelect.clearOptions();
                citySelect.addOptions(cities);
                citySelect.setValue('<?php echo e($city_id ?? ''); ?>');
            });
        });
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
</div><?php /**PATH D:\MyProjects\Benobe\panel\resources\views/livewire/admin/agent/create-agent.blade.php ENDPATH**/ ?>