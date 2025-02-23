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
    
        <h1 class="m-n font-thin h3">تخصص ها</h1>
    
        <a href="<?php echo e(route('admin.Dashboard.specialty.index')); ?>"
           class="btn btn-warning">بازگشت</a>
    </div>
    <div class="wrapper-md w-100">

        <div class="panel panel-default">
            <div class="panel-heading">ویرایش کردن تخصص</div>
            <div class="panel-body">

                <form method="POST"
                      action="<?php echo e(route('admin.Dashboard.specialty.update',$specialty->id)); ?>"
                      class="form-horizontal">
                    <?php echo csrf_field(); ?>
                    <div class="form-group">
                        <label class="control-label col-lg-2 mt-3">نام تخصص<span class="text-danger">*</span> </label>
                        <div class="mt-3"><input type="text"
                                   class="form-control"
                                   name="name"
                                   value="<?php echo e($specialty->name ? $specialty->name :  old('name')); ?>"></div>
                        <?php $__errorArgs = ['name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                        <?php echo e($message); ?>

                        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                    </div>
                    <input type="hidden"
                           name="parent_id"
                           value="0">
                           <input type="hidden" name="level" value="1">
                    <div class="col-lg-offset-2 mt-4"><button type="submit"
                                class="btn btn-success w-100 btn-lg">ویرایش کردن</button></div>
                </form>

            </div>

        </div>

    </div>
</div>

<?php $__env->stopSection(); ?>
<?php echo $__env->make('admin.content.layouts/layoutMaster', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\MyProjects\Benobe\panel\resources\views/admin/content/dashboard/specialty/edit.blade.php ENDPATH**/ ?>