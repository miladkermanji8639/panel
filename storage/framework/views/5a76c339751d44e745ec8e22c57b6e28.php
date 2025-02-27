<div class="panel panel-default">
    <div class="panel-heading">افزودن منو</div>
    <div class="panel-body">
        <!-- نمایش پیام موفقیت -->
    <!--[if BLOCK]><![endif]--><?php if($successMessage): ?>
        <div class="alert alert-success alert-dismissible fade show mt-3" role="alert">
            <?php echo e($successMessage); ?>

            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?><!--[if ENDBLOCK]><![endif]-->


        <form wire:submit.prevent="store">
            <div class="row">
                <!-- نام منو -->
                <div class="col-md-6">
                    <div class="form-group mb-3">
                        <label>نام منو</label>
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
                </div>

                <!-- لینک منو -->
                <div class="col-md-6">
                    <div class="form-group mb-3">
                        <label>لینک منو</label>
                        <input type="text" class="form-control" wire:model="url">
                        <!--[if BLOCK]><![endif]--><?php $__errorArgs = ['url'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <span class="text-danger"><?php echo e($message); ?></span> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><!--[if ENDBLOCK]><![endif]-->
                    </div>
                </div>

        <!-- آیکون (آپلود فایل) -->
        <div class="col-md-6">
            <div class="form-group mb-3">
                <label>آیکون</label>
                <input type="file" class="form-control" wire:model="icon">
                <!--[if BLOCK]><![endif]--><?php $__errorArgs = ['icon'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <span class="text-danger"><?php echo e($message); ?></span> <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?><!--[if ENDBLOCK]><![endif]-->
            </div>
        </div>


                <!-- جایگاه -->
                <div class="col-md-6">
                    <div class="form-group mb-3">
                        <label>جایگاه</label>
                        <select class="form-control" wire:model="position">
                            <option value="top">بالا</option>
                            <option value="bottom">پایین</option>
                            <option value="top_bottom">بالا و پایین</option>
                        </select>
                    </div>
                </div>

                <!-- زیرمجموعه -->
                <div class="col-md-6">
                    <div class="form-group mb-3">
                        <label>زیرمجموعه</label>
                        <select class="form-control" wire:model="parent_id">
                            <option value="">[دسته اصلی]</option>
                            <!--[if BLOCK]><![endif]--><?php $__currentLoopData = $menus; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $menu): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($menu->id); ?>"><?php echo e($menu->name); ?></option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><!--[if ENDBLOCK]><![endif]-->
                        </select>
                    </div>
                </div>

                <!-- ترتیب -->
                <div class="col-md-6">
                    <div class="form-group mb-3">
                        <label>ترتیب</label>
                        <input type="number" class="form-control" wire:model="order">
                    </div>
                </div>

                <!-- وضعیت -->
                <div class="col-md-6">
                    <div class="form-group mb-3">
                        <label>وضعیت</label>
                        <select class="form-control" wire:model="status">
                            <option value="1">فعال</option>
                            <option value="0">غیرفعال</option>
                        </select>
                    </div>
                </div>
            </div>

            <!-- دکمه‌ها -->
            <div class="d-flex justify-content-between mt-4">
                <a href="<?php echo e(route('admin.Dashboard.menu.index')); ?>" class="btn btn-warning">بازگشت</a>
                <button type="submit" class="btn btn-success">ثبت</button>
            </div>
        </form>
    </div>
</div>
<script>
    document.addEventListener('livewire:load', function () {
        Livewire.on('menuAdded', () => {
            setTimeout(() => {
                document.querySelector('.alert-success')?.remove();
            }, 5000); // حذف بعد از 5 ثانیه
        });
    });
</script><?php /**PATH D:\MyProjects\Benobe\panel\resources\views/livewire/admin/dashboard/menu/menu-form.blade.php ENDPATH**/ ?>