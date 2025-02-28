<div class="container-fluid py-4">
    <!-- هدر اصلی -->
    <div class="bg-light text-dark p-4 rounded-top border">
        <div class="d-flex align-items-center">
            <i class="fas fa-edit me-3"></i>
            <h5 class="mb-0 fw-bold">ویرایش اسلایدر</h5>
        </div>
    </div>

    <!-- بدنه اصلی -->
    <div class="bg-white p-4 rounded-bottom shadow-sm">
        <form wire:submit.prevent="update" enctype="multipart/form-data">
            <div class="row g-4">
                <!-- عنوان اسلاید -->
                <div class="col-md-6">
                    <div class="form-group">
                        <label class="form-label fw-bold">عنوان اسلاید</label>
                        <input type="text" class="form-control" wire:model="title">
                        <!--[if BLOCK]><![endif]--><?php $__errorArgs = ['title'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <span class="text-danger d-block mt-1"><?php echo e($message); ?></span> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><!--[if ENDBLOCK]><![endif]-->
                    </div>
                </div>

                <!-- تصویر -->
                <div class="col-md-6">
                    <div class="form-group">
                        <label class="form-label fw-bold">تصویر اسلایدر</label>
                        <!--[if BLOCK]><![endif]--><?php if($currentImage): ?>
                            <img src="<?php echo e(asset('storage/' . $currentImage)); ?>" alt="پیش‌نمایش"
                                style="max-width: 100px; height: auto; display: block; margin-bottom: 10px;">
                        <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                        <input type="file" class="form-control" wire:model="image" accept="image/*">
                        <!--[if BLOCK]><![endif]--><?php $__errorArgs = ['image'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <span class="text-danger d-block mt-1"><?php echo e($message); ?></span> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><!--[if ENDBLOCK]><![endif]-->
                    </div>
                </div>

                <!-- لینک -->
                <div class="col-md-6">
                    <div class="form-group">
                        <label class="form-label fw-bold">لینک نمایش</label>
                        <input type="text" class="form-control" wire:model="link">
                        <!--[if BLOCK]><![endif]--><?php $__errorArgs = ['link'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <span class="text-danger d-block mt-1"><?php echo e($message); ?></span> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><!--[if ENDBLOCK]><![endif]-->
                    </div>
                </div>

                <!-- توضیحات -->
                <div class="col-md-6">
                    <div class="form-group">
                        <label class="form-label fw-bold">توضیحات</label>
                        <textarea class="form-control" wire:model="description" rows="3"></textarea>
                        <!--[if BLOCK]><![endif]--><?php $__errorArgs = ['description'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <span class="text-danger d-block mt-1"><?php echo e($message); ?></span> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><!--[if ENDBLOCK]><![endif]-->
                    </div>
                </div>

                <!-- نمایش -->
                <div class="col-md-6">
                    <div class="form-group">
                        <label class="form-label fw-bold">قابل نمایش در</label>
                        <select class="form-control" wire:model="display">
                            <option value="site">سایت</option>
                            <option value="mobile">موبایل</option>
                        </select>
                        <!--[if BLOCK]><![endif]--><?php $__errorArgs = ['display'];
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
                <a href="<?php echo e(route('admin.content-management.slide.index')); ?>" class="btn btn-outline-warning">
                    <i class="fas fa-arrow-right me-2"></i> بازگشت
                </a>
                <button type="submit" class="btn btn-success">
                    <i class="fas fa-save me-2"></i> ویرایش
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
</div><?php /**PATH D:\MyProjects\Benobe\panel\resources\views/livewire/admin/content-management/slide-edit.blade.php ENDPATH**/ ?>