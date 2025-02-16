<?php

namespace App\Http\Controllers\Dr\Panel\Secretary;

use App\Models\Dr\Secretary;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class SecretaryManagementController
{
    public function index(Request $request)
    {
        $doctorId = Auth::guard('doctor')->user()->id ?? Auth::guard('secretary')->user()->doctor_id;
        $selectedClinicId = $request->input('selectedClinicId') ?? 'default';

        $secretaries = Secretary::where('doctor_id', $doctorId)
            ->when($selectedClinicId !== 'default', function ($query) use ($selectedClinicId) {
                // اگر کلینیک خاص انتخاب شد
                $query->where('clinic_id', $selectedClinicId);
            }, function ($query) {
                // اگر گزینه "default" انتخاب شد (عمومی و بدون کلینیک)
                $query->whereNull('clinic_id');
            })
            ->get();

        if ($request->ajax()) {
            return response()->json(['secretaries' => $secretaries]);
        }

        return view('dr.panel.secretary.index', compact('secretaries'));
    }



    public function store(Request $request)
    {
        // دریافت شناسه دکتر و کلینیک
        $doctorId = Auth::guard('doctor')->user()->id ?? Auth::guard('secretary')->user()->doctor_id;
        $clinicId = $request->selectedClinicId === 'default' ? null : $request->selectedClinicId;

        // اعتبارسنجی داده‌های ورودی
        $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'mobile' => [
                'required',
                function ($attribute, $value, $fail) use ($doctorId, $clinicId) {
                    // بررسی شرط شماره موبایل
                    $exists = Secretary::where('mobile', $value)
                        ->where('doctor_id', $doctorId)
                        ->where(function ($query) use ($clinicId) {
                        if ($clinicId) {
                            $query->where('clinic_id', $clinicId);
                        } else {
                            $query->whereNull('clinic_id');
                        }
                    })->exists();
                    if ($exists) {
                        $fail('این شماره موبایل قبلاً برای این دکتر یا کلینیک ثبت شده است.');
                    }
                },
            ],
            'national_code' => [
                'required',
                function ($attribute, $value, $fail) use ($doctorId, $clinicId) {
                    // بررسی شرط کد ملی
                    $exists = Secretary::where('national_code', $value)
                        ->where('doctor_id', $doctorId)
                        ->where(function ($query) use ($clinicId) {
                        if ($clinicId) {
                            $query->where('clinic_id', $clinicId);
                        } else {
                            $query->whereNull('clinic_id');
                        }
                    })->exists();
                    if ($exists) {
                        $fail('این کد ملی قبلاً برای این دکتر یا کلینیک ثبت شده است.');
                    }
                },
            ],
            'gender' => 'required|string',
            'password' => 'required|min:6',
        ]);

        try {
            // 👇 اینجا اطلاعات را به جدول ذخیره می‌کنیم
            $secretary = Secretary::create([
                'doctor_id' => $doctorId,
                'clinic_id' => $clinicId,
                'first_name' => $request->first_name,
                'last_name' => $request->last_name,
                'mobile' => $request->mobile,
                'national_code' => $request->national_code,
                'gender' => $request->gender,
                'password' => Hash::make($request->password),
            ]);

            // بعد از ذخیره، منشی‌های فعلی را برمی‌گردانیم
            $secretaries = Secretary::where('doctor_id', $doctorId)
                ->where(function ($query) use ($clinicId) {
                    if ($clinicId) {
                        $query->where('clinic_id', $clinicId);
                    } else {
                        $query->whereNull('clinic_id');
                    }
                })->get();

            return response()->json([
                'message' => 'منشی با موفقیت ثبت شد.',
                'secretary' => $secretary,
                'secretaries' => $secretaries,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'خطا در ثبت منشی!',
                'error' => $e->getMessage(),
            ], 500);
        }
    }












    public function edit(Request $request, $id)
    {
        $selectedClinicId = $request->input('selectedClinicId') ?? 'default';

        $secretary = Secretary::where('id', $id)
            ->when($selectedClinicId !== 'default', function ($query) use ($selectedClinicId) {
                $query->where('clinic_id', $selectedClinicId);
            })
            ->firstOrFail();

        return response()->json($secretary);
    }


    public function update(Request $request, $id)
    {
        $selectedClinicId = $request->input('selectedClinicId') ?? 'default';

        // اعتبارسنجی با شرط صحیح
        $request->validate([
            'first_name' => 'required',
            'last_name' => 'required',
            'mobile' => [
                'required',
                function ($attribute, $value, $fail) use ($id, $selectedClinicId) {
                    $exists = Secretary::where('mobile', $value)
                        ->where('id', '!=', $id)
                        ->where(function ($query) use ($selectedClinicId) {
                            if ($selectedClinicId !== 'default') {
                                $query->where('clinic_id', $selectedClinicId);
                            } else {
                                $query->whereNull('clinic_id');
                            }
                        })->exists();
                    if ($exists) {
                        $fail('این شماره موبایل قبلاً برای این کلینیک یا دکتر ثبت شده است.');
                    }
                },
            ],
            'national_code' => [
                'required',
                function ($attribute, $value, $fail) use ($id, $selectedClinicId) {
                    $exists = Secretary::where('national_code', $value)
                        ->where('id', '!=', $id)
                        ->where(function ($query) use ($selectedClinicId) {
                            if ($selectedClinicId !== 'default') {
                                $query->where('clinic_id', $selectedClinicId);
                            } else {
                                $query->whereNull('clinic_id');
                            }
                        })->exists();
                    if ($exists) {
                        $fail('این کد ملی قبلاً برای این کلینیک یا دکتر ثبت شده است.');
                    }
                },
            ],
            'gender' => 'required',
        ]);

        // پیدا کردن منشی برای ویرایش
        $secretary = Secretary::findOrFail($id);

        // به‌روزرسانی اطلاعات
        $secretary->update([
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'mobile' => $request->mobile,
            'national_code' => $request->national_code,
            'gender' => $request->gender,
            'password' => $request->password ? Hash::make($request->password) : $secretary->password,
        ]);

        // بازگردانی لیست منشی‌های به‌روزرسانی‌شده با فیلتر صحیح
        $secretaries = Secretary::where('doctor_id', $secretary->doctor_id)
            ->where(function ($query) use ($selectedClinicId) {
                if ($selectedClinicId !== 'default') {
                    $query->where('clinic_id', $selectedClinicId);
                } else {
                    $query->whereNull('clinic_id');
                }
            })
            ->get();

        return response()->json([
            'message' => 'منشی با موفقیت ویرایش شد.',
            'secretaries' => $secretaries,
        ]);
    }







    public function destroy(Request $request, $id)
    {
        $selectedClinicId = $request->input('selectedClinicId') ?? 'default';

        $secretary = Secretary::where('id', $id)
            ->when($selectedClinicId !== 'default', function ($query) use ($selectedClinicId) {
                $query->where('clinic_id', $selectedClinicId);
            })
            ->firstOrFail();

        $secretary->delete();

        $secretaries = Secretary::where('doctor_id', $secretary->doctor_id)
            ->when($selectedClinicId !== 'default', function ($query) use ($selectedClinicId) {
                $query->where('clinic_id', $selectedClinicId);
            })
            ->get();

        return response()->json(['message' => 'منشی با موفقیت حذف شد', 'secretaries' => $secretaries]);
    }

}
