<div>
    <!--[if BLOCK]><![endif]--><?php if(session('success')): ?>
        <div class="alert alert-success"><?php echo e(session('success')); ?></div>
    <?php endif; ?><!--[if ENDBLOCK]><![endif]-->

    <!--[if BLOCK]><![endif]--><?php if($services->isEmpty()): ?>
        <p>هیچ خدمتی یافت نشد.</p>
    <?php else: ?>
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>شناسه</th>
                    <th>نام خدمت</th>
                    <th>مدت زمان</th>
                    <th>قیمت</th>
                    <th>تخفیف</th>
                    <th>وضعیت</th>
                    <th>عملیات</th>
                    <th>زیر دسته</th>
                </tr>
            </thead>
            <tbody>
                <!--[if BLOCK]><![endif]--><?php $__currentLoopData = $services; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $service): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <?php echo $__env->make('dr.panel.dr-services.partials.service-row', ['service' => $service, 'level' => 0], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><!--[if ENDBLOCK]><![endif]-->
            </tbody>
        </table>
    <?php endif; ?><!--[if ENDBLOCK]><![endif]-->
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        document.querySelectorAll('.toggle-subservices').forEach(button => {
            button.addEventListener('click', function (event) {
                event.stopPropagation(); // جلوگیری از تداخل با دکمه‌های دیگر

                let serviceId = this.getAttribute('data-id');
                let subRows = document.querySelectorAll(`.subservice[data-parent='${serviceId}']`);
                let isOpened = this.classList.contains('opened');

                if (isOpened) {
                    closeSubservices(serviceId);
                    this.textContent = "مشاهده";
                    this.classList.remove('opened');
                } else {
                    subRows.forEach(row => row.style.display = 'table-row');
                    this.textContent = "بستن";
                    this.classList.add('opened');
                }
            });
        });

        function closeSubservices(parentId) {
            let subRows = document.querySelectorAll(`.subservice[data-parent='${parentId}']`);
            subRows.forEach(row => {
                row.style.display = 'none';
                let childButton = row.querySelector('.toggle-subservices');
                if (childButton) {
                    childButton.textContent = "مشاهده";
                    childButton.classList.remove('opened');
                    closeSubservices(row.getAttribute('data-id'));
                }
            });
        }

        document.querySelectorAll('.delete-service').forEach(button => {
            button.addEventListener('click', function (event) {
                event.preventDefault();

                let deleteUrl = this.getAttribute('data-url');
                let token = '<?php echo e(csrf_token()); ?>';

                Swal.fire({
                    title: 'آیا مطمئن هستید؟',
                    text: "این عملیات قابل بازگشت نیست!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6',
                    confirmButtonText: 'بله، حذف شود!',
                    cancelButtonText: 'لغو'
                }).then((result) => {
                    if (result.isConfirmed) {
                        fetch(deleteUrl, {
                            method: 'DELETE',
                            headers: {
                                'X-CSRF-TOKEN': token,
                                'Accept': 'application/json'
                            }
                        })
                            .then(response => response.json())
                            .then(() => {
                                Swal.fire('حذف شد!', 'سرویس با موفقیت حذف شد.', 'success')
                                    .then(() => {
                                        let rowToDelete = button.closest('tr');
                                        if (rowToDelete) rowToDelete.remove();
                                    });
                            });
                    }
                });
            });
        });
    });

</script><?php /**PATH D:\MyProjects\Benobe\panel\resources\views/livewire/dr/panel/doctor-services/doctor-services.blade.php ENDPATH**/ ?>