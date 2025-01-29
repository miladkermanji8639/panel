<?php

namespace App\Http\Middleware\Dr;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CheckSecretaryPermission
{
 public function handle(Request $request, Closure $next, ...$permissions) // تغییر تعداد آرگومان‌ها
 {
  $user = Auth::guard('secretary')->user();

  if (!$user || !array_intersect($permissions, $user->permissions->permissions ?? [])) {
   return abort(403, 'شما اجازه‌ی دسترسی ندارید.');
  }

  return $next($request);
 }
}
