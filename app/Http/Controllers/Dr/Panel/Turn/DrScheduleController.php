<?php
namespace App\Http\Controllers\Dr\Panel\Turn;

use App\Models\Dr\SubUser;
use Illuminate\Http\Request;
use Morilog\Jalali\Jalalian;
use App\Models\Dr\Appointment;
use Illuminate\Support\Facades\Auth;

class DrScheduleController
{
    /**
     * دریافت دکتر مرتبط با کاربر لاگین شده (پزشک یا منشی)
     */
    public function getAuthenticatedDoctor()
    {
        // بررسی پزشک لاگین شده
        $doctor = Auth::guard('doctor')->user();

        // اگر پزشک لاگین نکرده بود، بررسی دکتر مرتبط با منشی
        if (!$doctor) {
            $secretary = Auth::guard('secretary')->user();
            if ($secretary && $secretary->doctor) {
                $doctor = $secretary->doctor;
            }
        }

        // اگر دکتر مرتبط وجود نداشت، بازگشت مقدار null
        return $doctor;
    }

    /**
     * نمایش نوبت‌های امروز
     */
    public function index(Request $request)
    {
        // دریافت پزشک مرتبط
        $doctor = $this->getAuthenticatedDoctor();

        // اگر دکتر یافت نشد، دسترسی غیرمجاز است
        if (!$doctor) {
            abort(403, 'شما به این بخش دسترسی ندارید.');
        }

        // دریافت تاریخ امروز
        $now = \Carbon\Carbon::now()->format('Y-m-d');

        // دریافت نوبت‌ها
        $appointments = Appointment::with(['doctor', 'patient', 'insurance', 'clinic'])
            ->where('doctor_id', $doctor->id)
            ->where('appointment_date', $now)
            ->get();

        return view("dr.panel.turn.schedule.appointments", compact('appointments'));
    }

    /**
     * نمایش صفحه نوبت‌های من
     */
    public function myAppointments()
    {
        $doctor = $this->getAuthenticatedDoctor();

        if (!$doctor) {
            abort(403, 'شما به این بخش دسترسی ندارید.');
        }

        // دریافت لیست کاربران زیرمجموعه پزشک
        $subUserIds = SubUser::where('doctor_id', $doctor->id)
            ->pluck('user_id')
            ->toArray();

        // دریافت نوبت‌های کاربران زیرمجموعه
        $appointments = Appointment::with(['patient'])
            ->whereIn('patient_id', $subUserIds)
            ->orderBy('appointment_date', 'desc')
            ->paginate(10); // صفحه‌بندی

        return view("dr.panel.turn.schedule.my-appointments", compact('appointments'));
    }



    /**
     * دریافت نوبت‌ها بر اساس تاریخ انتخاب شده
     */
    public function showByDateAppointments(Request $request)
    {
        // دریافت پزشک مرتبط
        $doctor = $this->getAuthenticatedDoctor();

        // اگر دکتر یافت نشد، دسترسی غیرمجاز است
        if (!$doctor) {
            abort(403, 'شما به این بخش دسترسی ندارید.');
        }

        // دریافت تاریخ انتخاب شده یا پیش‌فرض به امروز
        $selectedDate = $request->input('date', Jalalian::now()->format('Y/m/d'));

        // بررسی فرمت تاریخ ورودی
        try {
            $gregorianDate = Jalalian::fromFormat('Y/m/d', $selectedDate)->toCarbon()->format('Y-m-d');
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'فرمت تاریخ وارد شده نامعتبر است.',
            ], 400);
        }

        // دریافت نوبت‌ها بر اساس تاریخ انتخاب شده
        $appointments = Appointment::with(['doctor', 'patient', 'insurance', 'clinic'])
            ->where('doctor_id', $doctor->id)
            ->where('appointment_date', '=', $gregorianDate)
            ->get();

        return response()->json([
            'appointments' => $appointments,
        ]);
    }

    public function show(string $id)
    {
        //
    }

    public function edit(string $id)
    {
        //
    }

    public function update(Request $request, string $id)
    {
        //
    }

    public function destroy($id)
    {
        //
    }
}
