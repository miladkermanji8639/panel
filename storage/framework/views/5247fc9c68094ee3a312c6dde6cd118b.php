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