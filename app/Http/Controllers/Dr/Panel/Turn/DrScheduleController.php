<?php
namespace App\Http\Controllers\Dr\Panel\Turn;
use Illuminate\Http\Request;
use App\Models\Dr\Appointment;
use Illuminate\Support\Facades\Auth;
class DrScheduleController
{
    public function getAuthenticatedDoctor()
    {
        $doctor = Auth::guard('doctor')->user();
        if (!$doctor) {
            abort(403, 'دسترسی غیرمجاز. لطفاً دوباره وارد شوید.');
        }
        return $doctor;
    }

    public function index(Request $request)
    {
        $doctor = $this->getAuthenticatedDoctor();

        if (!$doctor) {
            return redirect()->route('dr.auth.login-register-form')->with('error', 'لطفاً ابتدا وارد شوید.');
        }

        $now = \Carbon\Carbon::now()->format('Y-m-d');

        $appointments = Appointment::with(['doctor', 'patient', 'insurance', 'clinic'])
            ->where('doctor_id', $doctor->id)
            ->where('appointment_date', $now)
            ->get();

        return view("dr.panel.turn.schedule.appointments", compact('appointments'));
    }

    public function myAppointments(Request $request)
    {
        return view("dr.panel.turn.schedule.my-appointments");
    }
    public function showByDateAppointments(Request $request)
    {

        $doctor = $this->getAuthenticatedDoctor();
        $selectedDate = $request->input('date', \Morilog\Jalali\Jalalian::now()->format('Y/m/d'));
        // تبدیل تاریخ شمسی به میلادی برای جستجو در دیتابیس
        $gregorianDate = \Morilog\Jalali\Jalalian::fromFormat('Y/m/d', $selectedDate)->toCarbon();
        // فیلتر نوبت‌ها بر اساس تاریخ
        $appointments = Appointment::with(['doctor', 'patient', 'insurance', 'clinic'])
            ->where('doctor_id', $doctor->id)
            ->where('appointment_date', '=', $gregorianDate)
            ->get();
        return response()->json([
            'appointments' => $appointments
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
    }
}
