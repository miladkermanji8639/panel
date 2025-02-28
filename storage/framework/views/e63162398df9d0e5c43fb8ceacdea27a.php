<?php $__env->startSection('title', 'شهرها '); ?>

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

    <div class="content-wrapper">

        <!-- Content -->
        <div class="flex-grow-1  container-fluid">

            <div class="d-flex justify-content-between">
                <div>
                    <h4 class="py-3 mb-4">
                        <span class="text-muted fw-light">ناحیه ها /</span>
                        لیست شهرهای استان <?php echo e($cityName[0]->name); ?>

                    </h4>
                </div>

                <div>
                    <a href="<?php echo e(route('admin.Dashboard.cities.index')); ?>"
                       class="btn btn-warning">بازگشت</a>
                </div>
            </div>
            <div class="card">
                <div class="card-header d-flex justify-content-between">
                    <h5 class="card-title mb-0">لیست شهرها</h5>
                </div>

            <?php
$__split = function ($name, $params = []) {
    return [$name, $params];
};
[$__name, $__params] = $__split('admin.dashboard.cities.search-cities', ['provinceId' => $cityName[0]->id]);

$__html = app('livewire')->mount($__name, $__params, 'lw-3152225252-0', $__slots ?? [], get_defined_vars());

echo $__html;

unset($__html);
unset($__name);
unset($__params);
unset($__split);
if (isset($__slots)) unset($__slots);
?>

            </div>


        </div>
        

        


        <script type="text/javascript">
            // change status code
      function changeStatus(id){
                var element = $("#" + id)
                var url = element.attr('data-url')
                var elementValue = !element.prop('active');

                $.ajax({
                    url : url,
                    type : "GET",
                    success : function(response){
                        if(response.status){
                            if(response.active){
                                element.prop('active', true);
                                element.html('فعال')
                                element.removeClass('badge bg-label-danger')
                                element.addClass('badge bg-label-success')
                                successToast(' وضعیت شهر با موفقیت فعال شد')

                            }
                            else{
                                element.prop('active', false);
                                element.html('غیر فعال')
                                element.removeClass('badge bg-label-success')
                                element.addClass('badge bg-label-danger')
                                successToast(' وضعیت شهر با موفقیت غیر فعال شد')
                            }
                        }
                        else{
                            element.prop('active', elementValue);
                            errorToast('هنگام ویرایش مشکلی بوجود امده است')
                        }
                    },
                    error : function(){
                        element.prop('active', elementValue);
                        errorToast('ارتباط برقرار نشد')
                    }
                });

                function successToast(message){

                    var successToastTag = '<section class="toast" data-delay="5000">\n' +
                        '<section class="toast-body py-3 d-flex bg-success text-white">\n' +
                            '<strong class="ml-auto">' + message + '</strong>\n' +
                                '</section>\n' +
                                '</section>';

                                $('.toast-wrapper').append(successToastTag);
                                $('.toast').toast('show').delay(5500).queue(function() {
                                    $(this).remove();
                                })
                }

                function errorToast(message){

                    var errorToastTag = '<section class="toast" data-delay="5000">\n' +
                        '<section class="toast-body py-3 d-flex bg-danger text-white">\n' +
                            '<strong class="ml-auto">' + message + '</strong>\n' +
                                '</section>\n' +
                                '</section>';

                                $('.toast-wrapper').append(errorToastTag);
                                $('.toast').toast('show').delay(5500).queue(function() {
                                    $(this).remove();
                                })
                }
            }
            // end change status code



        </script>
        <!-- / Content -->

        <!-- Footer -->
        <!-- Footer-->

        <!--/ Footer-->
        <!-- / Footer -->
        <div class="content-backdrop fade"></div>
    </div>
    
<?php $__env->stopSection(); ?>
<?php echo $__env->make('admin.content.layouts/layoutMaster', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\MyProjects\Benobe\panel\resources\views/admin/content/dashboard/cities/show.blade.php ENDPATH**/ ?>