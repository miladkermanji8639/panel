<div class="container-fluid py-4">
    <!-- هدر اصلی -->
    <div class="bg-light text-dark p-4 rounded-top border">
        <div class="d-flex align-items-center">
            <i class="fas fa-plus me-3"></i>
            <h5 class="mb-0 fw-bold">افزودن خبر</h5>
        </div>
    </div>

    <!-- بدنه اصلی -->
    <div class="bg-white p-4 rounded-bottom shadow-sm">
        <form wire:submit.prevent="save" enctype="multipart/form-data">
            <div class="row g-4">
                <!-- عنوان خبر -->
                <div class="col-md-6">
                    <div class="form-group">
                        <label class="form-label fw-bold">عنوان خبر</label>
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

                <!-- دسته‌بندی -->
                <div class="col-md-6">
                    <div class="form-group">
                        <label class="form-label fw-bold">دسته‌بندی</label>
                        <select class="form-control" wire:model="category_id">
                            <option value="">انتخاب کنید</option>
                            <!--[if BLOCK]><![endif]--><?php $__currentLoopData = $categories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $id => $name): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($id); ?>"><?php echo e($name); ?></option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><!--[if ENDBLOCK]><![endif]-->
                        </select>
                        <!--[if BLOCK]><![endif]--><?php $__errorArgs = ['category_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <span class="text-danger d-block mt-1"><?php echo e($message); ?></span> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><!--[if ENDBLOCK]><![endif]-->
                    </div>
                </div>

                <!-- تاریخ -->
                <div class="col-md-6">
                    <div class="form-group">
                        <label class="form-label fw-bold">📅 تاریخ</label>
                        <input type="text" id="date-picker" class="form-control text-center" wire:model="selectedDate"
                            placeholder="مثلاً ۱۴۰۳/۰۱/۰۱">
                        <!--[if BLOCK]><![endif]--><?php $__errorArgs = ['selectedDate'];
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
                        <label class="form-label fw-bold">تصویر خبر</label>
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

                <!-- توضیح کوتاه -->
                <div class="col-md-12">
                    <div class="form-group">
                        <label class="form-label fw-bold">توضیح کوتاه</label>
                        <textarea class="form-control" wire:model="short_description" rows="3"></textarea>
                        <!--[if BLOCK]><![endif]--><?php $__errorArgs = ['short_description'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <span class="text-danger d-block mt-1"><?php echo e($message); ?></span> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><!--[if ENDBLOCK]><![endif]-->
                    </div>
                </div>

                <!-- متن خبر -->
                <div class="col-md-12">
                    <div class="form-group">
                        <label class="form-label fw-bold">متن خبر</label>
                        <textarea class="form-control" wire:model="content" rows="10"></textarea>
                        <!--[if BLOCK]><![endif]--><?php $__errorArgs = ['content'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <span class="text-danger d-block mt-1"><?php echo e($message); ?></span> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><!--[if ENDBLOCK]><![endif]-->
                    </div>
                </div>

                <!-- انتشار در صفحه اصلی -->
                <div class="col-md-6">
                    <div class="form-group">
                        <label class="form-label fw-bold">انتشار در صفحه اصلی</label>
                        <div class="form-check form-switch">
                            <input type="checkbox" class="form-check-input" wire:model="is_index" <?php if($is_index): echo 'checked'; endif; ?>>
                            <label class="form-check-label"><?php echo e($is_index ? 'بله' : 'خیر'); ?></label>
                        </div>
                        <!--[if BLOCK]><![endif]--><?php $__errorArgs = ['is_index'];
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

                <!-- Page Title -->
                <div class="col-md-6">
                    <div class="form-group">
                        <label class="form-label fw-bold">Page Title</label>
                        <input type="text" class="form-control" wire:model="page_title">
                        <!--[if BLOCK]><![endif]--><?php $__errorArgs = ['page_title'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <span class="text-danger d-block mt-1"><?php echo e($message); ?></span> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><!--[if ENDBLOCK]><![endif]-->
                    </div>
                </div>

                <!-- URL SEO -->
                <div class="col-md-6">
                    <div class="form-group">
                        <label class="form-label fw-bold">URL SEO</label>
                        <input type="text" class="form-control" wire:model="url_seo">
                        <!--[if BLOCK]><![endif]--><?php $__errorArgs = ['url_seo'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <span class="text-danger d-block mt-1"><?php echo e($message); ?></span> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><!--[if ENDBLOCK]><![endif]-->
                    </div>
                </div>

                <!-- Meta Description -->
                <div class="col-md-12">
                    <div class="form-group">
                        <label class="form-label fw-bold">Meta Description</label>
                        <textarea class="form-control" wire:model="meta_description" rows="3"></textarea>
                        <!--[if BLOCK]><![endif]--><?php $__errorArgs = ['meta_description'];
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
                <a href="<?php echo e(route('admin.content-management.blog.index')); ?>" class="btn btn-outline-warning">
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

 <script>
    document.addEventListener("DOMContentLoaded", function () {
        // مقدار initialDate رو از یه متغیر Blade که از کامپوننت میاد می‌گیریم
        const initialDate = <?php echo json_encode($selectedDate ?? '', 15, 512) ?>;

        flatpickr("#date-picker", {
            dateFormat: "Y/m/d",
            locale: "fa", // استفاده از لوکال فارسی
            defaultDate: initialDate,
            onChange: function (selectedDates, dateStr) {
                // به جای window.Livewire.find('<?php echo e($_instance->getId()); ?>') مستقیماً از emit استفاده می‌کنیم
                Livewire.dispatch('updateSelectedDate', { date: dateStr });
            }
        });

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
</div><?php /**PATH D:\MyProjects\Benobe\panel\resources\views/livewire/admin/content-management/blog-create.blade.php ENDPATH**/ ?>