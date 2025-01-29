<table class="table table-striped">
 <thead>
  <tr>
   <th>تاریخ</th>
   <th>ساعت ورود</th>
   <th>ساعت خروج</th>
   <th>وضعیت</th>
   <th>آی‌پی</th>
   <th>دستگاه</th>
   <th>حذف</th>
  </tr>
 </thead>
 <tbody>
  @foreach ($secretaryLogs as $log)
    <tr>
  <td>{{ \Carbon\Carbon::parse($log->login_at)->format('Y/m/d') }}</td>
  <td>{{ \Carbon\Carbon::parse($log->login_at)->format('H:i') }}</td>
  <td>{{ $log->logout_at ? \Carbon\Carbon::parse($log->logout_at)->format('H:i') : 'هنوز خارج نشده' }}</td>
  <td class="{{ $log->logout_at ? 'text-danger' : 'text-success' }}">
   {{ $log->logout_at ? 'آفلاین' : 'آنلاین' }}
  </td>
  <td>{{ $log->ip_address }}</td>
  <td>{{ $log->device }}</td>
  <td>
   <button class="btn btn-light btn-sm delete-log" data-id="{{ $log->id }}"><img src="{{ asset('dr-assets/icons/trash.svg') }}" alt=""></button>
  </td>
    </tr>
  @endforeach
 </tbody>
</table>
<div class="pagination-links">
 {{ $secretaryLogs->links() }}
</div>