<?php $__env->startSection('title', 'درگاه‌های پرداخت'); ?>

<?php $__env->startSection('vendor-style'); ?>
    <?php echo app('Illuminate\Foundation\Vite')(['resources/assets/vendor/libs/apex-charts/apex-charts.scss']); ?>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('vendor-script'); ?>
    <?php echo app('Illuminate\Foundation\Vite')(['resources/assets/vendor/libs/apex-charts/apexcharts.js']); ?>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('page-script'); ?>
    <?php echo app('Illuminate\Foundation\Vite')(['resources/assets/js/dashboards-crm.js']); ?>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
    <div class="container-fluid py-2">
        <!-- هدر -->
        <div class="bg-gradient-primary text-white p-4 rounded-top-3 shadow-sm mb-4">
            <div class="d-flex align-items-center justify-content-between gap-3">
                <div class="d-flex align-items-center gap-3">
                    <i class="fas fa-money-check-alt fs-4"></i>
                    <h4 class="mb-0 fw-semibold">درگاه‌های پرداخت</h4>
                </div>
            </div>
        </div>

        <!-- آلرت -->
        <div class="container px-0">
            <div class="alert alert-warning rounded-3 mb-5 d-flex align-items-center gap-3 shadow-sm">
                <i class="fas fa-info-circle fs-5 text-warning"></i>
                <span class="fw-medium">توجه: فقط یک درگاه می‌تواند فعال باشد.</span>
                <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>

            <!-- لیست درگاه‌ها -->
            <div class="row g-4">
                <?php $__currentLoopData = $gateways; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $gateway): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <div class="col-md-6 col-lg-4">
                        <div class="card border-0 rounded-3 shadow-sm gateway-card transition-all h-100">
                            <div class="card-body p-4 d-flex flex-column gap-3">
                                <!-- لوگو و نام -->
                                <div class="d-flex align-items-center gap-3">
                                    <div class="gateway-logo rounded-circle shadow-sm flex-shrink-0"
                                        style="background-image: url('<?php echo e(asset('dr-assets/icons/newspaper-svgrepo-com.svg')); ?>'); width: 50px; height: 50px; background-size: cover; background-position: center; border: 2px solid #e5e7eb; transition: transform 0.3s ease;"
                                        data-default-logo="https://cdn-icons-png.flaticon.com/512/888/888879.png">
                                    </div>
                                    <h5 class="mb-0 fw-medium text-dark"><?php echo e($gateway->title); ?></h5>
                                </div>

                                <!-- وضعیت و دکمه ویرایش -->
                                <div class="d-flex align-items-center justify-content-between mt-auto">
                                    <span
                                        class="status-btn toggle-gateway-status <?php echo e($gateway->is_active ? 'active' : 'inactive'); ?> px-3 py-2 rounded-pill d-flex align-items-center gap-2"
                                        data-gateway-id="<?php echo e($gateway->id); ?>" data-gateway-name="<?php echo e($gateway->name); ?>">
                                        <i class="<?php echo e($gateway->is_active ? 'fas fa-check' : 'fas fa-times'); ?> fs-6"></i>
                                        <?php echo e($gateway->is_active ? 'فعال' : 'غیرفعال'); ?>

                                    </span>
                                    <a href="<?php echo e(route('admin.Dashboard.payment_gateways.edit', $gateway->name)); ?>"
                                        class="btn btn-outline-primary btn-sm rounded-pill px-3 py-1 d-flex align-items-center gap-2">
                                        <i class="fas fa-edit fs-6"></i> ویرایش
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>
        </div>
    </div>

    <!-- استایل‌ها -->
    <style>
        .bg-gradient-primary {
            background: linear-gradient(90deg, #4f46e5, #7c3aed);
        }

        .gateway-card {
            background: #ffffff;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .gateway-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 15px rgba(0, 0, 0, 0.1);
        }

        .gateway-logo {
            transition: transform 0.3s ease;
        }

        .gateway-card:hover .gateway-logo {
            transform: scale(1.1);
        }

        .status-btn {
            font-weight: 500;
            cursor: pointer;
            transition: all 0.3s ease;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
        }

        .status-btn.active {
            background: linear-gradient(90deg, #10b981, #34d399);
            color: white;
        }

        .status-btn.active:hover {
            background: linear-gradient(90deg, #059669, #10b981);
        }

        .status-btn.inactive {
            background: linear-gradient(90deg, #f87171, #fca5a5);
            color: white;
        }

        .status-btn.inactive:hover {
            background: linear-gradient(90deg, #ef4444, #f87171);
        }

        .btn-outline-primary {
            border-color: #4f46e5;
            color: #4f46e5;
            transition: all 0.3s ease;
        }

        .btn-outline-primary:hover {
            background-color: #4f46e5;
            color: white;
            border-color: #4f46e5;
        }

        .alert-warning {
            background: #fef3c7;
            border: none;
            color: #92400e;
        }
    </style>

    <!-- اسکریپت‌ها -->
    <script>
        $(document).ready(function () {
            // جایگزینی لوگوی پیش‌فرض در صورت لود نشدن
            $('.gateway-logo').each(function () {
                const $element = $(this);
                const originalUrl = $element.css('background-image').replace('url("', '').replace('")', '');
                const defaultUrl = $element.data('default-logo');

                const img = new Image();
                img.onerror = () => $element.css('background-image', `url("${defaultUrl}")`);
                img.src = originalUrl;
            });

            // تغییر وضعیت درگاه
            $('.toggle-gateway-status').on('click', function () {
                const $badge = $(this);
                const gatewayId = $badge.data('gateway-id');
                const isActive = $badge.hasClass('active');

                $.ajax({
                    url: '<?php echo e(route("admin.payment_gateways.toggle")); ?>',
                    method: 'POST',
                    data: {
                        _token: '<?php echo e(csrf_token()); ?>',
                        gateway_id: gatewayId,
                        is_active: !isActive
                    },
                    success: function (response) {
                        if (response.success) {
                            $('.toggle-gateway-status')
                                .removeClass('active').addClass('inactive')
                                .html('<i class="fas fa-times fs-6"></i> غیرفعال');

                            if (response.is_active) {
                                $badge.removeClass('inactive').addClass('active')
                                    .html('<i class="fas fa-check fs-6"></i> فعال');
                                toastr.success('درگاه با موفقیت فعال شد!', { positionClass: 'toast-top-right', timeOut: 3000, progressBar: true });
                            } else {
                                toastr.info('درگاه با موفقیت غیرفعال شد!', { positionClass: 'toast-top-right', timeOut: 3000, progressBar: true });
                                if (response.default_activated === 'zarinpal') {
                                    $('.toggle-gateway-status[data-gateway-name="zarinpal"]')
                                        .removeClass('inactive').addClass('active')
                                        .html('<i class="fas fa-check fs-6"></i> فعال');
                                    toastr.warning('زرین‌پال به‌صورت خودکار فعال شد!', { positionClass: 'toast-top-right', timeOut: 3000, progressBar: true });
                                }
                            }
                        } else {
                            toastr.error('خطا در تغییر وضعیت!', { positionClass: 'toast-top-right', timeOut: 3000, progressBar: true });
                        }
                    },
                    error: () => toastr.error('خطایی رخ داد!', { positionClass: 'toast-top-right', timeOut: 3000, progressBar: true })
                });
            });
        });
    </script>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('admin.content.layouts/layoutMaster', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\MyProjects\Benobe\panel\resources\views/admin/content/dashboard/payment_gateways/index.blade.php ENDPATH**/ ?>