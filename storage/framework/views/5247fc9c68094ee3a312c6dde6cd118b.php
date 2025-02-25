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
            <div class="mt-3">
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
                                    <th>عملیات</th>
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
                                        <td><?php echo e($transaction->type === 'online' ? 'مشاوره آنلاین' : ($transaction->type === 'in_person' ? 'نوبت حضوری' : ($transaction->type === 'charge' ? 'شارژ کیف پول' : 'برداشت'))); ?>

                                        </td>
                                        <td><?php echo e($transaction->registered_at ? $transaction->registered_at->format('Y/m/d H:i') : '-'); ?>

                                        </td>
                                        <td><?php echo e($transaction->description ?? '-'); ?></td>
                                        <td>
                                            <button class="btn btn-light btn-sm delete-transaction rounded-circle"
                                                data-id="<?php echo e($transaction->id); ?>"><img src="<?php echo e(asset('dr-assets/icons/trash.svg')); ?>" alt="trash" srcset=""></button>
                                        </td>
                                    </tr>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                    <tr>
                                        <td colspan="7">موردی ثبت نشده است</td>
                                    </tr>
                                <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script>
        document.addEventListener('livewire:init', () => {
            toastr.options = {
                positionClass: 'toast-top-right',
                timeOut: 3000,
            };

            Livewire.on('toast', (event) => {
                toastr.success(event.message);
            });

            // مدیریت دکمه حذف با SweetAlert
            document.querySelectorAll('.delete-transaction').forEach(button => {
                button.addEventListener('click', function (e) {
                    e.preventDefault();
                    const transactionId = this.getAttribute('data-id');

                    Swal.fire({
                        title: 'آیا مطمئن هستید؟',
                        text: "این تراکنش حذف خواهد شد و قابل بازگشت نیست!",
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#3085d6',
                        cancelButtonColor: '#d33',
                        confirmButtonText: 'بله، حذف کن!',
                        cancelButtonText: 'خیر'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            window.Livewire.find('<?php echo e($_instance->getId()); ?>').call('deleteTransaction', transactionId);
                        }
                    });
                });
            });
        });
    </script>
</div><?php /**PATH D:\MyProjects\Benobe\panel\resources\views/livewire/dr/wallet-component.blade.php ENDPATH**/ ?>