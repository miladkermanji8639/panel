<div>
    <h4 class="py-3 mb-4">
        <span class="text-muted fw-light">تعرفه‌ها /</span> ویرایش تعرفه
    </h4>

    <!--[if BLOCK]><![endif]--><?php if($successMessage): ?>
        <div class="alert alert-success">
            <?php echo e($successMessage); ?>

        </div>
        <script>
            setTimeout(function () {
                window.location.href = "<?php echo e(route('admin.Dashboard.membershipfee.index')); ?>";
            }, 5000);
        </script>
    <?php endif; ?><!--[if ENDBLOCK]><![endif]-->

    <div class="card">
        <div class="card-header">
            <h5 class="card-title">ویرایش تعرفه</h5>
        </div>
        <div class="card-body">
            <form wire:submit.prevent="update">
                <div class="row">
                    <div class="col-md-6">
                        <label class="form-label">نام بسته</label>
                        <input type="text" class="form-control" wire:model="name">
                        <!--[if BLOCK]><![endif]--><?php $__errorArgs = ['name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <span class="text-danger"><?php echo e($message); ?></span> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><!--[if ENDBLOCK]><![endif]-->
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">تعداد روز</label>
                        <input type="number" class="form-control" wire:model="days">
                        <!--[if BLOCK]><![endif]--><?php $__errorArgs = ['days'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <span class="text-danger"><?php echo e($message); ?></span> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><!--[if ENDBLOCK]><![endif]-->
                    </div>

                    <div class="col-md-6 mt-3">
                        <label class="form-label">قیمت (تومان)</label>
                        <input type="number" class="form-control" wire:model="price">
                        <!--[if BLOCK]><![endif]--><?php $__errorArgs = ['price'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <span class="text-danger"><?php echo e($message); ?></span> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><!--[if ENDBLOCK]><![endif]-->
                    </div>

                    <div class="col-md-6 mt-3">
                        <label class="form-label">قرارگیری</label>
                        <input type="number" class="form-control" wire:model="sort">
                        <!--[if BLOCK]><![endif]--><?php $__errorArgs = ['sort'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <span class="text-danger"><?php echo e($message); ?></span> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><!--[if ENDBLOCK]><![endif]-->
                    </div>

                    <div class="col-12 mt-4">
                        <button type="submit" class="btn btn-success">بروزرسانی تعرفه</button>
                        <a href="<?php echo e(route('admin.Dashboard.membershipfee.index')); ?>"
                            class="btn btn-secondary">بازگشت</a>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div><?php /**PATH D:\MyProjects\Benobe\panel\resources\views/livewire/membership-fee-edit.blade.php ENDPATH**/ ?>