<?php
namespace App\Http\Controllers\Dr\Panel\Profile;

use App\Models\User;
use App\Models\Dr\SubUser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class SubUserController
{
 public function index()
 {
  $doctorId = Auth::guard('doctor')->id();
  $subUsers = SubUser::with('user')->where('doctor_id', $doctorId)->get();

  // دریافت کاربرانی که هنوز به عنوان ساب‌یوزر ثبت نشده‌اند
  $users = User::all();

  return view('dr.panel.profile.subuser', compact('subUsers', 'users'));
 }

 public function store(Request $request)
 {
  $validator = Validator::make($request->all(), [
   'user_id' => 'required|exists:users,id',
  ]);

  if ($validator->fails()) {
   return response()->json(['errors' => $validator->errors()], 422);
  }

  $doctorId = Auth::guard('doctor')->id();

  $existingSubUser = SubUser::where('doctor_id', $doctorId)
   ->where('user_id', $request->user_id)
   ->first();

  if ($existingSubUser) {
   return response()->json(['error' => 'این کاربر قبلاً اضافه شده است!'], 422);
  }

  SubUser::create([
   'doctor_id' => $doctorId,
   'user_id' => $request->user_id,
  ]);

  return response()->json([
   'message' => 'کاربر زیرمجموعه با موفقیت اضافه شد!',
   'subUsers' => SubUser::where('doctor_id', $doctorId)->with('user')->get(),
   'users' => User::all(),

  ]);
 }



 public function edit($id)
 {
  $subUser = SubUser::with('user')->findOrFail($id);
  $users = User::all(); // لیست تمام کاربران برای نمایش در سلکت

  return response()->json([
   'id' => $subUser->id,
   'user_id' => $subUser->user_id,
   'users' => $users, // این مقدار در فرانت‌اند استفاده می‌شود
  ]);
 }


 public function update(Request $request, $id)
 {
  $subUser = SubUser::findOrFail($id);

  $validator = Validator::make($request->all(), [
   'user_id' => 'required|exists:users,id',
  ]);

  if ($validator->fails()) {
   return response()->json(['errors' => $validator->errors()], 422);
  }

  if ($subUser->user_id == $request->user_id) {
   return response()->json(['message' => 'بدون تغییر! مقدار جدید همان مقدار قبلی است.']);
  }

  $existingSubUser = SubUser::where('doctor_id', $subUser->doctor_id)
   ->where('user_id', $request->user_id)
   ->first();

  if ($existingSubUser) {
   return response()->json(['error' => 'این کاربر قبلاً به لیست اضافه شده است!'], 422);
  }

  $subUser->user_id = $request->user_id;
  $subUser->save();

  return response()->json([
   'message' => 'کاربر زیرمجموعه با موفقیت ویرایش شد!',
   'subUsers' => SubUser::where('doctor_id', $subUser->doctor_id)->with('user')->get(),
   'users' => User::all(),

  ]);
 }




 public function destroy($id)
 {
  $subUser = SubUser::findOrFail($id);
  $doctorId = $subUser->doctor_id;
  $subUser->delete();

  return response()->json([
   'message' => 'کاربر زیرمجموعه حذف شد!',
   'subUsers' => SubUser::where('doctor_id', $doctorId)->with('user')->get(),
  ]);
 }
}