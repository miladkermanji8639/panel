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
    <div class="container-fluid py-4">
        <div class="card shadow-lg border-0 rounded-3">
            <div
                class="card-header bg-gradient-primary text-white d-flex align-items-center justify-content-between px-4 py-3">
                <div class="d-flex align-items-center">
                    <i class="fas fa-money-check-alt me-3"></i>
                    <h5 class="mb-0 fw-bold">درگاه‌های پرداخت</h5>
                </div>
            </div>
            <div class="card-body p-4">
                <div class="alert alert-warning alert-dismissible fade show rounded-3 mb-4" role="alert">
                    <i class="fa fa-info-circle me-2"></i>
                    توجه داشته باشید شما فقط یک درگاه فعال می‌توانید داشته باشید.
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
                <div class="table-responsive rounded-3 shadow-sm">
                    <table class="table table-hover table-striped align-middle mb-0">
                        <thead class="table-dark bg-gradient-dark">
                            <tr>
                                <th scope="col" class="text-center py-3" style="width: 100px;">عملیات</th>
                                <th scope="col" class="text-center py-3" style="width: 80px;">آرم</th>
                                <th scope="col" class="py-3">نام درگاه</th>
                                <th scope="col" class="text-center py-3" style="width: 120px;">وضعیت</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $__currentLoopData = $gateways; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $gateway): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <tr class="transition-all hover:bg-gray-100">
                                    <td class="text-center">
                                        <a href="<?php echo e(route('admin.Dashboard.payment_gateways.edit', $gateway->name)); ?>"
                                            class="btn btn-outline-primary btn-sm rounded-pill px-3" title="ویرایش">
                                            <i class="fa fa-edit"></i>
                                        </a>
                                    </td>
                                    <td class="text-center">
                                        <div class="gateway-logo rounded-circle shadow-sm mx-auto"
                                            style="background-image: url('<?php echo e($gateway->logo); ?>'); width: 45px; height: 45px; background-size: cover; background-position: center; border: 3px solid #dee2e6; transition: transform 0.3s ease;"
                                            data-default-logo="https://cdn-icons-png.flaticon.com/512/888/888879.png"
                                            onmouseover="this.style.transform='scale(1.1)'"
                                            onmouseout="this.style.transform='scale(1)'">
                                        </div>
                                    </td>
                                    <td class="fw-medium"><?php echo e($gateway->title); ?></td>
                                    <td class="text-center">
                                        <span
                                            class="badge toggle-gateway-status <?php echo e($gateway->is_active ? 'bg-success' : 'bg-danger'); ?> text-white py-2 px-4 rounded-pill fw-bold"
                                            data-gateway-id="<?php echo e($gateway->id); ?>" data-gateway-name="<?php echo e($gateway->name); ?>"
                                            style="cursor: pointer; transition: all 0.3s ease; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
                                            <?php echo e($gateway->is_active ? 'فعال' : 'غیرفعال'); ?>

                                        </span>
                                    </td>
                                </tr>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <script>
        $(document).ready(function () {
            // چک کردن لوگوها و جایگزینی با پیش‌فرض اگه لود نشدن
            $('.gateway-logo').each(function () {
                var $element = $(this);
                var originalUrl = $element.css('background-image').replace('url("', '').replace('")', '');
                var defaultUrl = $element.data('default-logo');

                // تست لود شدن تصویر
                var img = new Image();
                img.onload = function () {
                };
                img.onerror = function () {
                    $element.css('background-image', 'url("' + defaultUrl + '")');
                };
                img.src = originalUrl;
            });

            // مدیریت تغییر وضعیت درگاه‌ها
            $('.toggle-gateway-status').on('click', function () {
                var gatewayId = $(this).data('gateway-id');
                var badge = $(this);
                var isActive = badge.hasClass('bg-success');

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
                                .removeClass('bg-success')
                                .addClass('bg-danger')
                                .text('غیرفعال');

                            if (response.is_active) {
                                badge.removeClass('bg-danger').addClass('bg-success').text('فعال');
                                toastr.success('درگاه با موفقیت فعال شد!', {
                                    positionClass: 'toast-top-right',
                                    timeOut: 3000,
                                    progressBar: true
                                });
                            } else {
                                toastr.info('درگاه با موفقیت غیرفعال شد!', {
                                    positionClass: 'toast-top-right',
                                    timeOut: 3000,
                                    progressBar: true
                                });
                                if (response.default_activated === 'zarinpal') {
                                    $('.toggle-gateway-status[data-gateway-name="zarinpal"]')
                                        .removeClass('bg-danger')
                                        .addClass('bg-success')
                                        .text('فعال');
                                    toastr.warning('زرین‌پال به‌صورت خودکار فعال شد چون هیچ درگاهی فعال نبود!', {
                                        positionClass: 'toast-top-right',
                                        timeOut: 3000,
                                        progressBar: true
                                    });
                                }
                            }
                        } else {
                            toastr.error('خطا در تغییر وضعیت درگاه!', {
                                positionClass: 'toast-top-right',
                                timeOut: 3000,
                                progressBar: true
                            });
                        }
                    },
                    error: function () {
                        toastr.error('خطایی رخ داد. لطفاً دوباره تلاش کنید!', {
                            positionClass: 'toast-top-right',
                            timeOut: 3000,
                            progressBar: true
                        });
                    }
                });
            });
        });
    </script>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('admin.content.layouts/layoutMaster', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\MyProjects\Benobe\panel\resources\views/admin/content/dashboard/payment_gateways/index.blade.php ENDPATH**/ ?>