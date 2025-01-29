<?php

namespace App\Http\Controllers\Dr\Panel\Secretary;

use App\Models\Dr\Secretary;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class SecretaryManagementController
{
    public function index()
    {
        $doctorId = Auth::guard('doctor')->user()->id ?? Auth::guard('secretary')->user()->doctor_id; // شناسه دکتر لاگین‌شده
        $secretaries = Secretary::where('doctor_id', $doctorId)->get();
        return view('dr.panel.secretary.index', compact('secretaries'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'first_name' => 'required',
            'last_name' => 'required',
            'mobile' => [
                'required',
                'unique:secretaries',
                function ($attribute, $value, $fail) {
                    if (\DB::table('doctors')->where('mobile', $value)->exists()) {
                        $fail('شماره موبایل قبلاً به عنوان دکتر ثبت شده است.');
                    }
                },
            ],
            'national_code' => [
                'required',
                'unique:secretaries',
                function ($attribute, $value, $fail) {
                    if (\DB::table('doctors')->where('national_code', $value)->exists()) {
                        $fail('کد ملی قبلاً به عنوان دکتر ثبت شده است.');
                    }
                },
            ],
            'gender' => 'required',
            'password' => 'required|min:6',
        ]);

        Secretary::create([
            'doctor_id' => Auth::guard('doctor')->user()->id ?? Auth::guard('secretary')->user()->doctor_id,
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'mobile' => $request->mobile,
            'national_code' => $request->national_code,
            'gender' => $request->gender,
            'password' => Hash::make($request->password),
        ]);

        $secretaries = Secretary::where('doctor_id', Auth::guard('doctor')->user()->id ?? Auth::guard('secretary')->user()->doctor_id)->get();

        return response()->json(['message' => 'منشی با موفقیت اضافه شد', 'secretaries' => $secretaries]);
    }



    public function edit($id)
    {
        $secretary = Secretary::findOrFail($id);
        return response()->json($secretary);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'first_name' => 'required',
            'last_name' => 'required',
            'mobile' => [
                'required',
                "unique:secretaries,mobile,$id",
                function ($attribute, $value, $fail) {
                    if (\DB::table('doctors')->where('mobile', $value)->exists()) {
                        $fail('شماره موبایل قبلاً به عنوان دکتر ثبت شده است.');
                    }
                },
            ],
            'national_code' => [
                'required',
                "unique:secretaries,national_code,$id",
                function ($attribute, $value, $fail) {
                    if (\DB::table('doctors')->where('national_code', $value)->exists()) {
                        $fail('کد ملی قبلاً به عنوان دکتر ثبت شده است.');
                    }
                },
            ],
            'gender' => 'required',
        ]);

        $secretary = Secretary::findOrFail($id);
        $secretary->update([
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'mobile' => $request->mobile,
            'national_code' => $request->national_code,
            'gender' => $request->gender,
            'password' => $request->password ? Hash::make($request->password) : $secretary->password,
        ]);

        $secretaries = Secretary::where('doctor_id', Auth::guard('doctor')->user()->id ?? Auth::guard('secretary')->user()->doctor_id)->get();

        return response()->json([
            'message' => 'منشی با موفقیت به‌روزرسانی شد',
            'secretaries' => $secretaries,
        ]);
    }




    public function destroy($id)
    {
        $secretary = Secretary::findOrFail($id);
        $secretary->delete();
        $secretaries = Secretary::where('doctor_id', Auth::guard('doctor')->user()->id ?? Auth::guard('secretary')->user()->doctor_id)->get();
        return response()->json(['message' => 'منشی با موفقیت حذف شد', 'secretaries' => $secretaries]);
    }
}
