<div class="card p-3 shadow-sm">
    <h5 class="mb-3">مدیریت تعطیلات</h5>

    <div class="row">
        <div class="col-md-6 mb-3">
            <label for="holidayPicker" class="form-label fw-bold">📅 انتخاب تاریخ تعطیلی</label>
            <input type="text" id="holidayPicker" class="form-control text-center" wire:model="selectedDate" placeholder="مثلاً ۱۴۰۳/۰۱/۰۱">
        </div>

        <div class="col-md-6 mb-3">
            <label for="title" class="form-label fw-bold">🏷 عنوان تعطیلی</label>
            <input type="text" id="title" class="form-control text-center" wire:model="title" placeholder="مثلاً عید نوروز">
        </div>
    </div>

    <button class="btn btn-success w-100" wire:click="addHoliday">✅ ثبت تعطیلی</button>

    <hr class="my-4">

    <h5 class="mb-3">📜 تعطیلات ثبت‌شده:</h5>

    <ul class="list-group">
        <!--[if BLOCK]><![endif]--><?php $__currentLoopData = $holidaysList; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $holiday): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <li class="list-group-item d-flex justify-content-between align-items-center">
                <span>📅 <?php echo e(verta($holiday->date)->format('Y/m/d')); ?> - <?php echo e($holiday->title ?? 'بدون عنوان'); ?></span>
            <button class="btn btn-danger btn-sm" wire:click="removeHoliday('<?php echo e($holiday->date); ?>')">حذف</button>

            </li>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><!--[if ENDBLOCK]><![endif]-->
    </ul>

    <script>
        document.addEventListener("DOMContentLoaded", function () {
            flatpickr("#holidayPicker", {
                dateFormat: "Y-m-d",
                locale: "fa", // نمایش ماه شمسی
                disable: <?php echo json_encode($holidays, 15, 512) ?>,
                onChange: function (selectedDates, dateStr) {
                    window.Livewire.find('<?php echo e($_instance->getId()); ?>').set('selectedDate', dateStr);
                }
            });
        });
    </script>
</div>
<?php /**PATH D:\MyProjects\Benobe\panel\resources\views/livewire/admin/dashboard/holiday/holiday-manager.blade.php ENDPATH**/ ?>