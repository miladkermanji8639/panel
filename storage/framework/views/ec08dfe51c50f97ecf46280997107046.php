<?php $__env->startSection('title', 'افزودن گزارش کیف پول جدید   '); ?>

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

        <div class="bg-white-only lter b-b wrapper-md clrfix">

          <h1 class="m-n font-thin h3"> گزارش کیف پول</h1>


        </div>
        <div class="wrapper-md w-100">

          <div class="panel panel-default">
            <div class="panel-heading">اضافه کردن گزارش کیف پول</div>
            <div class="panel-body">

              <form method="post"
                    action="?mod=zone"
                    class="form-horizontal">
                <div class="form-group">
                  <label class="control-label col-lg-2 mt-3">تاریخ ثبت<span class="text-danger">*</span> </label>
                  <div class="col-lg-12 mt-3"><input type="text"
                           class="form-control"
                           name="name"></div>
                </div>
                <div class="form-group">
                  <label class="control-label col-lg-2 mt-3"> مبلغ<span class="text-danger">*</span> </label>
                  <div class="col-lg-12 mt-3"><input type="text"
                           class="form-control"
                           name="name"></div>
                </div>
                <div class="form-group">
                  <label class="control-label col-lg-2 mt-3">وضعیت</label>
                  <div class="col-lg-12 mt-3"><select name="status"
                            class="form-control">
                      <option value="1">فعال</option>
                      <option value="0">غیرفعال</option>
                      <option value="2">درانتظار درخواست</option>
                    </select> </div>
                </div>
                <div class="form-group">
                  <label class="control-label col-lg-2 mt-3">توضیح :</label>
                  <div class="col-lg-12 mt-3"><input type="text"
                           name="price_shipping"
                           class="form-control numberkey"
                           placeholder="   "></div>
                </div>
                <div class="col-lg-offset-2 mt-4"><button type="submit"
                          class="btn btn-success w-100 btn-lg">اضافه کردن</button></div>
              </form>

            </div>

          </div>

        </div>
      </div>
      <?php $__env->stopSection(); ?>

<?php echo $__env->make('admin.content.layouts/layoutMaster', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\MyProjects\Benobe\panel\resources\views/admin/content/agent/create.blade.php ENDPATH**/ ?>