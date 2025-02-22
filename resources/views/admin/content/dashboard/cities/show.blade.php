@extends('admin.content.layouts/layoutMaster')

@section('title', 'شهرها ')

@section('vendor-style')
@vite(['resources/assets/vendor/libs/apex-charts/apex-charts.scss'])
@endsection

@section('vendor-script')
@vite(['resources/assets/vendor/libs/apex-charts/apexcharts.js'])
@endsection

@section('page-script')
@vite(['resources/assets/js/dashboards-crm.js'])
@endsection

{{-- <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script> --}}

@section('content')

    <div class="content-wrapper">

        <!-- Content -->
        <div class="flex-grow-1  container-fluid">

            <div class="d-flex justify-content-between">
                <div>
                    <h4 class="py-3 mb-4">
                        <span class="text-muted fw-light">ناحیه ها /</span>
                        لیست شهرهای استان {{ $cityName[0]->name }}
                    </h4>
                </div>

                <div>
                    <a href="{{ route('admin.Dashboard.cities.index') }}"
                       class="btn btn-warning">بازگشت</a>
                </div>
            </div>
            <div class="card">
                <div class="card-header d-flex justify-content-between">
                    <h5 class="card-title mb-0">لیست شهرها</h5>
                </div>

            @livewire('search-cities', ['provinceId' => $cityName[0]->id])

            </div>


        </div>
        {{-- ajax search --}}

        {{-- ajax search --}}


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
    {{-- @include('admin.content.alerts.sweetalert.delete-confirm',['className'=>'delete']) --}}
@endsection