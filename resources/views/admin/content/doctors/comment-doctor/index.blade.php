@extends('admin.content.layouts/layoutMaster')

@section('title', 'نظرات پزشکان - مدیریت نظرات')



@section('page-script')
  @vite(['resources/assets/js/dashboards-crm.js'])
@endsection

@section('content')
  <div class="container-fluid py-4">
    <header class="glass-header p-3 rounded-3 mb-2 shadow-lg">
    <div class="d-flex align-items-center justify-content-between gap-3">
      <div class="d-flex align-items-center gap-2">
      <i class="fas fa-comments fs-4 text-white animate-bounce"></i>
      <h4 class="mb-0 fw-bold text-white">نظرات پزشکان</h4>
      </div>
      <div class="text-white fw-medium fs-6">جستجو و مدیریت نظرات</div>
    </div>
    </header>

    @livewire('admin.doctors.comment-doctor.admin-doctors-comment-index')

  </div>
@endsection