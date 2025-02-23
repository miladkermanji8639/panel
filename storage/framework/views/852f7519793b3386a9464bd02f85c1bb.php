<?php $__env->startSection('title', 'افزودن ناحیه جدید '); ?>

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
<div class="app-content-body">

    <div class="bg-white-only lter b-b wrapper-md clrfix d-flex justify-content-between">
    
        <h1 class="m-n font-thin h3">ناحیه ها</h1>
    
        <a href="<?php echo e(route('admin.Dashboard.cities.index')); ?>"
           class="btn btn-warning">بازگشت</a>
    </div>
    <div class="wrapper-md w-100">

        <div class="panel panel-default">
            <div class="panel-heading">اضافه کردن ناحیه</div>
            <div class="panel-body">

                <form method="POST"
                      action="<?php echo e(route('admin.Dashboard.cities.store-city')); ?>"
                      action="?mod=zone"
                      class="form-horizontal">
                    <?php echo csrf_field(); ?>
                    <div class="form-group">
                        <label class="control-label col-lg-2 mt-3">نام<span class="text-danger">*</span> </label>
                        <div class="col-lg-12 mt-3">
                            <input type="text"
                                   class="form-control"
                                   name="name"
                                   value="<?php echo e(old('name')); ?>">
                            <?php $__errorArgs = ['name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                            <span class="text-danger"><?php echo e($message); ?></span>
                            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label col-lg-2 mt-3">استان</label>
                        <div class="col-lg-12 mt-3">
                            <select class="chosen-select  chosen-rtl form-control"
                                    name="parent_id">
                                <?php $__currentLoopData = $cities; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $city): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($city->id); ?>"><?php echo e($city->name); ?></option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

                            </select>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="control-label col-lg-2 mt-3">قیمت حمل و نقل و ارسال کالا به این منطقه:</label>
                        <div class="col-lg-12 mt-3"><input type="text"
                                   name="price_shipping"
                                   class="form-control numberkey"
                                   value="<?php echo e(old('price_shipping')); ?>"
                                   placeholder="در صورت رایگان بودن 0 وارد کنید"></div>
                                   <?php $__errorArgs = ['price_shipping'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                <span class="text-danger"><?php echo e($message); ?></span>
                                <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                    </div>
                    <div class="form-group">
                        <input type="hidden" name="level" value="2">
                        </div>
                      </div>
                    <div class="col-lg-offset-2 mt-4"><button type="submit"
                                class="btn btn-success w-100 btn-lg">اضافه کردن</button></div>
                </form>

            </div>

        </div>

    </div>
</div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('admin.content.layouts/layoutMaster', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\MyProjects\Benobe\panel\resources\views/admin/content/dashboard/cities/create-city.blade.php ENDPATH**/ ?>