<?php
namespace App\Http\Controllers\Dr\Panel;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Morilog\Jalali\Jalalian;
use App\Models\Dr\Appointment;
use Hekmatinasser\Verta\Verta;
use Morilog\Jalali\CalendarUtils;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

class DrPanelController
{
  /**
   * Display a listing of the resource.
   */
  public function index()
  {
    $today = Carbon::today();
    $doctorId = Auth::guard('doctor')->user()->id; // گرفتن ID پزشک لاگین‌شده

    // تعداد بیماران امروز فقط برای این پزشک
    $totalPatientsToday = Appointment::where('doctor_id', $doctorId)
      ->whereDate('appointment_date', $today)
      ->count();

    // بیماران ویزیت شده فقط برای این پزشک
    $visitedPatients = Appointment::where('doctor_id', $doctorId)
      ->whereDate('appointment_date', $today)
      ->where('attendance_status', 'attended')
      ->count();

    // بیماران باقی‌مانده فقط برای این پزشک
    $remainingPatients = $totalPatientsToday - $visitedPatients;

    return view("dr.panel.index", compact('totalPatientsToday', 'visitedPatients', 'remainingPatients'));
  }

  public function getAppointmentsByDate(Request $request)
  {
    $jalaliDate = $request->input('date'); // دریافت تاریخ جلالی از فرانت‌اند

    // **اصلاح فرمت تاریخ ورودی**
    if (strpos($jalaliDate, '-') !== false) {
      // اگر تاریخ با `-` جدا شده بود، آن را به `/` تبدیل کنیم
      $jalaliDate = str_replace('-', '/', $jalaliDate);
    }

    // بررسی صحت فرمت تاریخ ورودی (1403/11/24)
    if (!preg_match('/^\d{4}\/\d{2}\/\d{2}$/', $jalaliDate)) {
      return response()->json(['error' => 'فرمت تاریخ جلالی نادرست است.'], 400);
    }

    // **تبدیل تاریخ جلالی به میلادی**
    try {
      $gregorianDate = Jalalian::fromFormat('Y/m/d', $jalaliDate)->toCarbon()->format('Y-m-d');
    } catch (\Exception $e) {
      return response()->json(['error' => 'خطا در تبدیل تاریخ جلالی به میلادی.'], 500);
    }

    // لاگ‌گیری برای بررسی تبدیل صحیح

    $doctorId = Auth::guard('doctor')->user()->id; // دریافت ID پزشک لاگین‌شده

    // گرفتن نوبت‌های پزشک جاری در تاریخ تبدیل‌شده به میلادی
    $appointments = Appointment::where('doctor_id', $doctorId)
      ->whereDate('appointment_date', $gregorianDate)
      ->with(['patient', 'insurance']) // گرفتن اطلاعات بیمار و بیمه
      ->get();

    return response()->json([
      'appointments' => $appointments
    ]);
  }
  public function searchPatients(Request $request)
  {
    $query = $request->query('query'); // مقدار جستجو شده
    $date = $request->query('date'); // تاریخ انتخاب شده

    $patients = Appointment::with('patient', 'insurance')
      ->whereDate('appointment_date', $date)
      ->whereHas('patient', function ($q) use ($query) {
        $q->where('first_name', 'like', "%$query%")
          ->orWhere('last_name', 'like', "%$query%")
          ->orWhereRaw("CONCAT(first_name, ' ', last_name) LIKE ?", ["%$query%"])
          ->orWhere('mobile', 'like', "%$query%")
          ->orWhere('national_code', 'like', "%$query%");
      })
      ->get();

    return response()->json(['patients' => $patients]);
  }




  /**
   * Show the form for creating a new resource.
   */
  public function create()
  {
    //
  }
  /**
   * Store a newly created resource in storage.
   */
  public function store(Request $request)
  {
    //
  }
  /**
   * Display the specified resource.
   */
  public function show(string $id)
  {
    //
  }
  /**
   * Show the form for editing the specified resource.
   */
  public function edit(string $id)
  {
    //
  }
  /**
   * Update the specified resource in storage.
   */
  public function update(Request $request, string $id)
  {
    //
  }
  /**
   * Remove the specified resource from storage.
   */
  public function destroy(string $id)
  {
    //

  }
}
