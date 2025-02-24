<script src="{{ asset('admin-assets/js/jquery/jquery.min.js') }}"></script>

@vite(['resources/assets/vendor/fonts/tabler-icons.scss', 'resources/assets/vendor/fonts/fontawesome.scss', 'resources/assets/vendor/fonts/flag-icons.scss'])
<!-- Core CSS -->
@vite(['resources/assets/vendor/scss' . $configData['rtlSupport'] . '/core' . ($configData['style'] !== 'light' ? '-' . $configData['style'] : '') . '.scss', 'resources/assets/vendor/scss' . $configData['rtlSupport'] . '/' . $configData['theme'] . ($configData['style'] !== 'light' ? '-' . $configData['style'] : '') . '.scss', 'resources/assets/css/demo.css'])
@vite(['resources/assets/vendor/libs/node-waves/node-waves.scss', 'resources/assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.scss', 'resources/assets/vendor/libs/typeahead-js/typeahead.scss'])
<!-- Vendor Styles -->
@yield('vendor-style')
<!-- Page Styles -->
@yield('page-style')

<link rel="stylesheet" href="{{ asset('admin-assets/css/choosen/choosen.min.css') }}">
<link rel="stylesheet" href="{{ asset('dr-assets/panel/css/toastr/toastr.min.css') }}">
<link rel="stylesheet" href="{{ asset('dr-asset/panel/css/tom-select.bootstrap5.min.css') }}">
<style>
      .h-50{
            height: 50px !important;
      }
</style>