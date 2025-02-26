<?php $__env->startSection('title', 'ویرایش درگاه پرداخت - <?php echo e($gateway->title); ?>'); ?>

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
                    <h5 class="mb-0 fw-bold">ویرایش درگاه: <?php echo e($gateway->title); ?></h5>
                </div>
                <a href="<?php echo e(route('admin.Dashboard.payment_gateways.index')); ?>"
                    class="btn btn-outline-light btn-sm rounded-pill">
                    <i class="fa fa-arrow-right me-2"></i> بازگشت
                </a>
            </div>
            <div class="card-body p-4">
                <form action="<?php echo e(route('admin.payment_gateways.update', $gateway->name)); ?>" method="POST" class="row g-3">
                    <?php echo csrf_field(); ?>
                    <?php echo method_field('PUT'); ?>
                    <div class="col-md-6">
                        <div class="card bg-light border-0 shadow-sm rounded-3 p-3">
                            <div class="d-flex align-items-center mb-3">
                                <div class="gateway-logo rounded-circle shadow-sm me-3"
                                    style="background-image: url('<?php echo e($gateway->logo); ?>'); width: 50px; height: 50px; background-size: cover; background-position: center; border: 3px solid #dee2e6;">
                                </div>
                                <h6 class="mb-0 fw-bold text-dark"><?php echo e($gateway->title); ?></h6>
                            </div>
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" id="isActive" name="is_active" value="1" <?php echo e($gateway->is_active ? 'checked' : ''); ?>>
                                <label class="form-check-label" for="isActive">وضعیت:
                                    <?php echo e($gateway->is_active ? 'فعال' : 'غیرفعال'); ?></label>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="card bg-light border-0 shadow-sm rounded-3 p-3">
                            <label for="settings" class="form-label fw-bold text-dark">تنظیمات (JSON)</label>
                            <textarea dir="ltr" class="form-control" id="settings" name="settings" rows="8"
                                placeholder="تنظیمات را به‌صورت JSON وارد کنید"
                                style="resize: vertical; font-family: monospace;"><?php echo e(json_encode(json_decode($gateway->settings), JSON_PRETTY_PRINT)); ?></textarea>
                            <small class="text-muted mt-2 d-block">مثال: {"merchant_id": "xxxx", "sandbox": true}</small>
                        </div>
                    </div>
                    <div class="col-12 text-end mt-4">
                        <button type="submit" class="btn btn-primary btn-lg  px-5">
                            <i class="fa fa-save me-2"></i> ذخیره تغییرات
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('admin.content.layouts/layoutMaster', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\MyProjects\Benobe\panel\resources\views/admin/content/dashboard/payment_gateways/edit.blade.php ENDPATH**/ ?>