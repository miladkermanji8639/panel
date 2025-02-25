<div class="main-content">
    <div class="container-fluid bg-white p-2">
        <div class="user-panel-content mt-3">
            <div class="alert alert-warning">
                <p><i class="fa fa-info-circle fa-2x"></i> صرفاً مبالغ هزینه‌های نوبت حضوری که تاریخ آنها رسیده است و
                    مشاوره‌های آنلاینی که پاسخ داده شده‌اند، قابل برداشت می‌باشند و مابقی در حالت انتظار می‌باشند.</p>
            </div>
            <div class="wallet_totalprice">
                <i class="mdi mdi-wallet"></i> جمع مبلغ قابل برداشت: <?php echo e(number_format($availableAmount)); ?> تومان
                <br>
                <button wire:click="requestSettlement" class="btn reqazadsazi btn-success h-50 mt-3">درخواست
                    آزادسازی</button>
            </div>
            <div class="card mt-3">
                <div class="card-header"><span>کیف پول</span></div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped table_middle">
                            <thead>
                                <tr>
                                    <th>ردیف</th>
                                    <th>مبلغ</th>
                                    <th>وضعیت</th>
                                    <th>نوع</th>
                                    <th>تاریخ ثبت</th>
                                    <th>شرح</th>
                                </tr>
                            </thead>
                            <tbody>
                                <!--[if BLOCK]><![endif]--><?php $__empty_1 = true; $__currentLoopData = $transactions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $transaction): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                    <tr>
                                        <td><?php echo e($index + 1); ?></td>
                                        <td><?php echo e(number_format($transaction->amount)); ?> تومان</td>
                                        <td>
                                            <!--[if BLOCK]><![endif]--><?php if($transaction->status === 'pending'): ?>
                                                <label class="badge badge-primary">در انتظار ارائه خدمت</label>
                                            <?php elseif($transaction->status === 'available'): ?>
                                                <label class="badge badge-outline-green">قابل برداشت</label>
                                            <?php elseif($transaction->status === 'requested'): ?>
                                                <label class="badge badge-warning">درخواست‌شده</label>
                                            <?php elseif($transaction->status === 'paid'): ?>
                                                <label class="badge badge-success">پرداخت‌شده</label>
                                            <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                                        </td>
                                        <td><?php echo e($transaction->type === 'online' ? 'مشاوره آنلاین' : 'نوبت حضوری'); ?></td>
                                        <td><?php echo e($transaction->registered_at ? $transaction->registered_at->format('Y/m/d H:i') : '-'); ?>

                                        </td>
                                        <td><?php echo e($transaction->description ?? '-'); ?></td>
                                    </tr>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                    <tr>
                                        <td colspan="6">موردی ثبت نشده است</td>
                                    </tr>
                                <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
    <script>
        document.addEventListener('livewire:init', () => {
            toastr.options = {
                positionClass: 'toast-top-right',
                timeOut: 3000,
                closeButton: true,
            };

            Livewire.on('toast', (event) => {
                toastr.success(event.message);
            });
        });
    </script>
</div><?php /**PATH D:\MyProjects\Benobe\panel\resources\views/livewire/dr/wallet-component.blade.php ENDPATH**/ ?>