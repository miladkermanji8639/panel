@extends('admin.content.layouts/layoutMaster')
@section('title', 'استانها ')
@section('vendor-style')
 @vite(['resources/assets/vendor/libs/apex-charts/apex-charts.scss'])
@endsection
@section('vendor-script')
 @vite(['resources/assets/vendor/libs/apex-charts/apexcharts.js'])
@endsection
@section('page-script')

 @vite(['resources/assets/js/dashboards-crm.js'])
@endsection
@section('content')
 <div class="content-wrapper">
  <!-- Content -->
  <div class="flex-grow-1  container-fluid">
   <h4 class="mb-4">
    <span class="text-muted fw-light">ناحیه ها /</span>
    لیست استانها
   </h4>
   <div class="card">
    <div class="card-header">
     <h5 class="card-title mb-0">لیست استانها</h5>
    </div>
    <div class="card-datatable table-responsive">
     <div id="DataTables_Table_0_wrapper" class="dataTables_wrapper dt-bootstrap5 no-footer">
      <div class="card-header d-flex border-top rounded-0 flex-wrap py-2 justify-content-between w-100">
       <div class="ms-n2 me-5 pe-5">
        <div id="DataTables_Table_0_filter w-100" class="dataTables_filter">
         <input type="search" class="form-control h-50 w-100" placeholder="جستجو استان" id="searchZone" name="search"
          value="">
        </div>
       </div>
       <div class="d-flex justify-content-start justify-content-md-end align-items-baseline">
        <div
         class="dt-action-buttons d-flex flex-column align-items-start align-items-md-center justify-content-sm-center mb-md-0 gap-sm-0 flex-sm-row mb-3 gap-4 pt-0">
         <div class="dt-buttons btn-group flex-wrap">
          <button class="btn btn-secondary add-new btn-primary ms-sm-0 waves-effect waves-light ms-2" tabindex="0"
           onclick="location.href='{{ route('admin.Dashboard.cities.create') }}'" aria-controls="DataTables_Table_0"
           type="button"><span><i class="ti ti-plus me-sm-1 ti-xs me-0"></i><span
             class="d-none d-sm-inline-block">افزودن ناحیه </span></span></button>
         </div>
        </div>
       </div>
      </div>
      <div class="table-responsive text-nowrap">
       <table class="table  table-striped" id="tbl_search">
        <thead>
         <tr>
          <th class="sorting_disabled dt-checkboxes-cell dt-checkboxes-select-all" id="dt-checkboxes-select-all"
           rowspan="1" colspan="1" style="width: 18px;" data-col="1" aria-label=""><input type="checkbox"
            class="form-check-input"></th>
          <th>کد استان</th>
          <th>نام استان</th>
          <th>وضعیت</th>
          <th>عملیات</th>
         </tr>
        </thead>
        <tbody class="all-data" id="tbody">
         @foreach ($cities as $city)
          <tr>
           <td class="sorting_disabled dt-checkboxes-cell dt-checkboxes-select-all" rowspan="1" colspan="1"
            style="width: 18px;" data-col="1" aria-label=""><input type="checkbox" class="form-check-input"></td>
           <td>
            <span class="fw-medium"> {{ $city->id }}</span>
           </td>
           <td> {{ $city->name }}</td>
           <td>
            <span id="{{ $city->id }}" title="برای فعال یا غیر فعالسازی وضعیت کلیک کنید"
             onclick="changeStatus({{ $city->id }})"
             data-url="{{ route('admin.Dashboard.cities.status', $city->id) }}"
             class="badge bg-label-{{ $city->status == 1 ? 'success' : 'danger' }} me-1 cursor-pointer"
             {{ $city->status === 1 ? 'active' : 'deactive' }}>
             {{ $city->status === 0 ? 'غیر فعال' : 'فعال' }}
            </span>
           </td>
           <td>
            <div class="dropdown">
             <button class="btn dropdown-toggle hide-arrow p-0" data-bs-toggle="dropdown" type="button">
              <i class="ti ti-dots-vertical"></i>
             </button>
             <div class="dropdown-menu">
              <a class="dropdown-item" onclick="location.href='{{ url('admin/dashboard/cities/show/' . $city->id) }}'"
               href="javascript:void(0);">
               <i class="ti ti-eye me-1"></i>
               شهرستان
              </a>
              <a class="dropdown-item" onclick="location.href='{{ url('admin/dashboard/cities/edit/' . $city->id) }}'"
               href="javascript:void(0);">
               <i class="ti ti-pencil me-1"></i>
               ویرایش
              </a>
              <form method="POST" class="" action="{{ url('admin/dashboard/cities/delete/' . $city->id) }}">
               {{ csrf_field() }}
               {{ method_field('DELETE') }}
               <button type="submit" class="dropdown-item delete" id="delete">
                <i class="ti ti-trash me-1"></i>
                حذف
               </button>
              </form>
             </div>
            </div>
           </td>
          </tr>
         @endforeach
        </tbody>
       </table>
      </div>
      <div class="row mx-2 mt-4">
       <div class="col-sm-12 col-md-6">
        <div class="dataTables_paginate paging_simple_numbers" id="DataTables_Table_0_paginate">
         {{ $cities->links('pagination::bootstrap-5') }}
        </div>
       </div>
      </div>
      <div style="width: 1%;"></div>
     </div>
    </div>
   </div>
  </div>
  {{-- ajax search --}}
  @include('admin.content.my-tools.ajax-search', ['route_name' => 'admin.Dashboard.cities.search-zone'])
  {{-- ajax search --}}


  <div class="content-backdrop fade"></div>
 </div>
 <script>
  // change status code
  function changeStatus(id) {
   var element = $("#" + id)
   var url = element.attr('data-url')
   var elementValue = !element.prop('active');
   $.ajax({
    url: url,
    type: "GET",
    success: function(response) {
     if (response.status) {
      if (response.active) {
       element.prop('active', true);
       element.html('فعال')
       element.removeClass('badge bg-label-danger')
       element.addClass('badge bg-label-success')
       toastr.success(' وضعیت استان با موفقیت فعال شد')
      } else {
       element.prop('active', false);
       element.html('غیر فعال')
       element.removeClass('badge bg-label-success')
       element.addClass('badge bg-label-danger')
       toastr.success(' وضعیت استان با موفقیت غیر فعال شد')
      }
     } else {
      element.prop('active', elementValue);
      toastr.error('هنگام ویرایش مشکلی بوجود امده است')
     }
    },
    error: function() {
     element.prop('active', elementValue);
     toastr.error('ارتباط برقرار نشد')
    }
   });
  }
 </script>

@endsection
