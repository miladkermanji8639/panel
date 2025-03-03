@extends('admin.content.layouts.layoutMaster')
@section('title', 'ایجاد  پزشک')
@section('content')
  <div class="container-fluid py-1">
    <header class="glass-header p-3 rounded-3 mb-2 shadow-lg">
    <div class="d-flex align-items-center justify-content-between gap-3">
      <div class="d-flex align-items-center gap-2">
      <i class="fas fa-user-md fs-4 text-white animate-bounce"></i>
      <h4 class="mb-0 fw-bold text-white">ایجاد اطلاعات پزشک</h4>
      </div>
      <div class="text-white fw-medium fs-6">ایجاد اطلاعات پزشک</div>
    </div>
    </header>
    @livewire('admin.doctors.doctors-management-create')
  </div>
@endsection