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
        $doctorId = Auth::guard('doctor')->user()->id; // شناسه دکتر لاگین‌شده
        $secretaries = Secretary::where('doctor_id', $doctorId)->get();
        return view('dr.panel.secretary.index', compact('secretaries'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'first_name' => 'required',
            'last_name' => 'required',
            'mobile' => 'required|unique:secretaries',
            'national_code' => 'required|unique:secretaries',
            'gender' => 'required',
            'password' => 'required|min:6',
        ]);

        Secretary::create([
            'doctor_id' => Auth::guard('doctor')->user()->id,
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'mobile' => $request->mobile,
            'national_code' => $request->national_code,
            'gender' => $request->gender,
            'password' => Hash::make($request->password) ?? NULL,
        ]);
        $secretaries = Secretary::where('doctor_id', Auth::guard('doctor')->user()->id)->get();

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
            'mobile' => "required|unique:secretaries,mobile,$id",
            'national_code' => "required|unique:secretaries,national_code,$id",
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

        // استفاده از doctor_id برای بازیابی لیست منشی‌ها
        $secretaries = Secretary::where('doctor_id', Auth::guard('doctor')->user()->id)->get();

        return response()->json([
            'message' => 'منشی با موفقیت به‌روزرسانی شد',
            'secretaries' => $secretaries,
        ]);
    }


    public function destroy($id)
    {
        $secretary = Secretary::findOrFail($id);
        $secretary->delete();
        $secretaries = Secretary::where('doctor_id', Auth::guard(name: 'doctor')->user()->id)->get();
        return response()->json(['message' => 'منشی با موفقیت حذف شد', 'secretaries' => $secretaries]);
    }
}
