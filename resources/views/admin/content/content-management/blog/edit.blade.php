@extends('admin.content.layouts.layoutMaster')

@section('title', 'مدیریت اخبار')

@section('vendor-style')
  @vite([
  'resources/assets/vendor/libs/fullcalendar/fullcalendar.scss',
  'resources/assets/vendor/libs/flatpickr/flatpickr.scss',
  'resources/assets/vendor/libs/select2/select2.scss',
  'resources/assets/vendor/libs/quill/editor.scss',
  'resources/assets/vendor/libs/@form-validation/form-validation.scss',
  ])
@endsection

@section('page-style')
  @vite(['resources/assets/vendor/scss/pages/app-calendar.scss'])
@endsection

@section('vendor-script')
  @vite([
  'resources/assets/vendor/libs/fullcalendar/fullcalendar.js',
  'resources/assets/vendor/libs/@form-validation/popular.js',
  'resources/assets/vendor/libs/@form-validation/auto-focus.js',
  'resources/assets/vendor/libs/select2/select2.js',
  'resources/assets/vendor/libs/jdate/jdate.min.js',
  'resources/assets/vendor/libs/flatpickr/flatpickr-jdate.js',
  'resources/assets/vendor/libs/flatpickr-jalali/dist/l10n/fa.js',
  'resources/assets/vendor/libs/moment/moment.js',
  ])
@endsection




@section('content')
  @livewire('admin.content-management.blog-edit', ['id' => $id])
@endsection