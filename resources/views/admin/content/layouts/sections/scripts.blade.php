<!-- BEGIN: Vendor JS-->

@vite(['resources/assets/vendor/libs/jquery/jquery.js', 'resources/assets/vendor/libs/popper/popper.js', 'resources/assets/vendor/js/bootstrap.js', 'resources/assets/vendor/libs/node-waves/node-waves.js', 'resources/assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.js', 'resources/assets/vendor/libs/hammer/hammer.js', 'resources/assets/vendor/libs/typeahead-js/typeahead.js', 'resources/assets/vendor/js/menu.js'])

@yield('vendor-script')
<!-- END: Page Vendor JS-->
<!-- BEGIN: Theme JS-->
@vite(['resources/assets/js/main.js'])

<!-- END: Theme JS-->
<!-- Pricing Modal JS-->
@stack('pricing-script')
<!-- END: Pricing Modal JS-->
<!-- BEGIN: Page JS-->
@yield('page-script')

<script src="{{ asset('app-assets/js/town-city/city.js') }}"></script>

<script src="{{ asset('admin-assets/js/dashboard/dashboard-setting.js') }}"></script>
<script src="{{ asset('admin-assets/js/users-edit/users-edit.js') }}"></script>
<script src="{{ asset('admin-assets/js/user-group-edit/user-group-edit.js') }}"></script>
<script src="{{ asset('dr-assets/js/select2/select2.js') }}"></script>
<script src="{{ asset('dr-assets/panel/js/toastr/toastr.min.js') }}"></script>
<script src="{{ asset('dr-assets/panel/js/sweetalert2/sweetalert2.js') }}"></script>
<script src="{{ asset('dr-asset/panel/js/tom-select.complete.min.js') }}"></script>

<script type="text/javascript">
 $("#nameid").select2({
  placeholder: "Select a Name",
  allowClear: true
 });
</script>
<!-- END: Page JS-->
