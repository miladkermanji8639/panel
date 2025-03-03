@extends('admin.content.layouts/layoutMaster')

@section('title', 'ویرایش نظر - مدیریت نظرات')



@section('page-script')
    @vite(['resources/assets/js/dashboards-crm.js'])
@endsection

@section('content')
    <div class="container-fluid py-4">
        @livewire('admin.doctors.comment-doctor.admin-doctors-comment-show', ['id' => $id])
    </div>
@endsection