<div class="container-fluid py-4">
    <!-- هدر اصلی -->
    <div class="bg-light text-dark p-4 rounded-top border">
        <div class="d-flex align-items-center justify-content-between">
            <div class="d-flex align-items-center">
                <i class="fas fa-newspaper me-3"></i>
                <h5 class="mb-0 fw-bold">لیست اخبار</h5>
            </div>
            <a href="<?php echo e(route('admin.content-management.blog.create')); ?>" class="btn btn-primary">
                <i class="fas fa-plus me-2"></i> افزودن خبر
            </a>
        </div>
    </div>

    <!-- بدنه اصلی -->
    <div class="bg-white p-4 rounded-bottom shadow-sm">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div class="input-group w-25">
                <span class="input-group-text"><i class="fas fa-search"></i></span>
                <input type="text" class="form-control" wire:model.live="search" placeholder="جستجو عنوان یا توضیح">
            </div>
            <div class="d-flex align-items-center gap-3">
                <select wire:model.live="perPage" class="form-select w-auto">
                    <option value="10">10</option>
                    <option value="20">20</option>
                    <option value="50">50</option>
                    <option value="100">100</option>
                </select>
                <button class="btn btn-danger" id="delete-selected-btn" <?php if(empty($selectedBlogs)): ?> disabled <?php endif; ?>>
                    <i class="fas fa-trash me-2"></i> حذف انتخاب‌شده‌ها
                </button>
            </div>
        </div>

        <div class="table-responsive">
            <table class="table table-bordered table-hover">
                <thead class="table-light">
                    <tr>
                        <th style="width: 50px;">
                            <input type="checkbox" class="form-check-input" wire:model.live="selectAll">
                        </th>
                        <th>ردیف</th>
                        <th>تاریخ</th>
                        <th>تصویر</th>
                        <th>عنوان</th>
                        <th>دسته‌بندی</th>
                        <th>وضعیت</th>
                        <th>بازدید</th>
                        <th>نظرات</th>
                        <th>عملیات</th>
                    </tr>
                </thead>
                <tbody>
                    <!--[if BLOCK]><![endif]--><?php $__empty_1 = true; $__currentLoopData = $blogs; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $blog): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <tr>
                            <td>
                                <input type="checkbox" class="form-check-input" wire:model.live="selectedBlogs"
                                    value="<?php echo e($blog->id); ?>">
                            </td>
                            <td><?php echo e($blogs->firstItem() + $index); ?></td>
                            <td><?php echo e($blog->persian_date); ?></td>
                            <td>
                                <!--[if BLOCK]><![endif]--><?php if($blog->image): ?>
                                    <img src="<?php echo e(asset('storage/' . $blog->image)); ?>" alt="<?php echo e($blog->title); ?>"
                                        style="max-width: 50px; height: auto;">
                                <?php else: ?>
                                    بدون تصویر
                                <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                            </td>
                            <td><?php echo e($blog->title); ?></td>
                            <td><?php echo e($blog->category ? $blog->category->name : 'بدون دسته‌بندی'); ?></td>
                            <td>
                                <div class="form-check form-switch">
                                    <input type="checkbox" class="form-check-input"
                                        wire:model.live="blogStatuses.<?php echo e($blog->id); ?>"
                                        wire:change="toggleStatus(<?php echo e($blog->id); ?>)" <?php if($blog->status): echo 'checked'; endif; ?>>
                                    <label class="form-check-label mx-1"><?php echo e($blog->status ? 'فعال' : 'غیرفعال'); ?></label>
                                </div>
                            </td>
                            <td><?php echo e($blog->views); ?></td>
                            <td><?php echo e($blog->comments_count); ?></td>
                            <td>
                                <div class="dropdown">
                                    <button class="btn p-0 dropdown-toggle hide-arrow" data-bs-toggle="dropdown">
                                        <i class="fas fa-ellipsis-v"></i>
                                    </button>
                                    <div class="dropdown-menu">
                                        <a class="dropdown-item"
                                            href="<?php echo e(route('admin.content-management.blog.edit', $blog->id)); ?>">
                                            <i class="fas fa-edit me-2"></i> ویرایش
                                        </a>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <tr>
                            <td colspan="10" class="text-center">هیچ خبری یافت نشد.</td>
                        </tr>
                    <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                </tbody>
            </table>
        </div>

        <!-- صفحه‌بندی -->
        <div class="mt-4">
            <?php echo e($blogs->links('pagination::bootstrap-5')); ?>

        </div>
    </div>

    <!-- استایل‌ها -->
    <style>
        .bg-light {
            background-color: #f8f9fa !important;
        }

        .border {
            border-color: #dee2e6 !important;
        }

        .form-control:focus,
        .form-select:focus {
            border-color: #007bff;
            box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25);
        }

        .btn {
            border-radius: 0.375rem;
            padding: 0.75rem 1.5rem;
        }

        .btn-danger {
            background-color: #dc3545;
            border-color: #dc3545;
        }

        .btn-danger:hover {
            background-color: #c82333;
            border-color: #bd2130;
        }

        .btn-danger:disabled {
            background-color: #6c757d;
            border-color: #6c757d;
            cursor: not-allowed;
        }

        .form-switch .form-check-input {
            width: 2.5em;
            height: 1.25em;
        }
    </style>

    <!-- اسکریپت SweetAlert و Toastr -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css" rel="stylesheet">

    <script>
        document.addEventListener('livewire:initialized', () => {
            document.getElementById('delete-selected-btn').addEventListener('click', function (e) {
                e.preventDefault();
                Swal.fire({
                    title: 'آیا مطمئن هستید؟',
                    text: "این اخبار حذف خواهند شد و قابل بازگشت نیستند!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#dc3545',
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: 'بله، حذف کن',
                    cancelButtonText: 'خیر'
                }).then((result) => {
                    if (result.isConfirmed) {
                        window.Livewire.find('<?php echo e($_instance->getId()); ?>').call('deleteSelected');
                        console.log('Delete confirmed');
                    }
                });
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
</div><?php /**PATH D:\MyProjects\Benobe\panel\resources\views/livewire/admin/content-management/blog-list.blade.php ENDPATH**/ ?>