<?php
namespace App\Http\Controllers\Dr\Panel\Turn\Schedule\ScheduleSetting;
use App\Models\Dr\Vacation;
use Illuminate\Http\Request;
use Morilog\Jalali\CalendarUtils;
use Morilog\Jalali\Jalalian;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

class VacationController
{

 public function index(Request $request)
 {
  $doctorId = Auth::guard('doctor')->user()->id ?? Auth::guard('secretary')->user()->doctor_id ;

  try {
   // استفاده از متد has برای بررسی وجود پارامترها
   $year = $request->has('year') ? $request->input('year') : null;
   $month = $request->has('month') ? $request->input('month') : null;

   // اگر سال یا ماه null بود، از تاریخ جاری استفاده کن
   if ($year === null || $month === null) {
    $currentJalaliDate = Jalalian::now();
    $year = $year ?? $currentJalaliDate->getYear();
    $month = $month ?? $currentJalaliDate->getMonth();
   }


   // اطمینان از دو رقمی بودن ماه
   $month = str_pad($month, 2, '0', STR_PAD_LEFT);

   // تبدیل دقیق‌تر سال و ماه جلالی به میلادی
   $jalaliStartDate = Jalalian::fromFormat('Y/m/d', "{$year}/{$month}/01");
   $jalaliEndDate = $jalaliStartDate->addMonths(1)->subDays(1);

   $gregorianStartDate = $jalaliStartDate->toCarbon();
   $gregorianEndDate = $jalaliEndDate->toCarbon();



   $query = Vacation::where('doctor_id', $doctorId)
    ->whereBetween('date', [
     $gregorianStartDate->format('Y-m-d'),
     $gregorianEndDate->format('Y-m-d')
    ]);

   $vacations = $query->get();

   if ($request->ajax()) {
    return response()->json([
     'success' => true,
     'vacations' => $vacations,
     'year' => $year,
     'month' => $month
    ]);
   }

   return view("dr.panel.turn.schedule.scheduleSetting.vacation", compact('vacations'));
  } catch (\Exception $e) {


   return response()->json([
    'success' => false,
    'message' => 'خطا در پردازش تاریخ‌ها: ' . $e->getMessage(),
   ], 500);
  }
 }





 public function edit($id)
 {
  $vacation = Vacation::where('id', $id)
   ->where('doctor_id', Auth::guard('doctor')->user()->id ?? Auth::guard('secretary')->user()->doctor_id )
   ->firstOrFail();

  return response()->json(['success' => true, 'vacation' => $vacation]);
 }

 public function store(Request $request)
 {
  $doctorId = Auth::guard('doctor')->user()->id ?? Auth::guard('secretary')->user()->doctor_id ;

  $validatedData = $request->validate([
   'date' => 'required|date',
   'start_time' => 'nullable|date_format:H:i',
   'end_time' => 'nullable|date_format:H:i|after:start_time',
   'is_full_day' => 'nullable|boolean',
  ]);

  // تبدیل تاریخ جلالی به میلادی
  $gregorianDate = CalendarUtils::createDatetimeFromFormat('Y/m/d', $request->date)->format('Y-m-d');
  $validatedData['date'] = $gregorianDate;

  // مقداردهی پیش‌فرض برای روز کامل
  if ($request->is_full_day) {
   $validatedData['start_time'] = '00:00';
   $validatedData['end_time'] = '23:00';
  }

  // بررسی تکراری بودن مرخصی
  $exists = Vacation::where('doctor_id', $doctorId)
   ->where('date', $validatedData['date'])
   ->where(function ($query) use ($validatedData) {
    $query->whereBetween('start_time', [$validatedData['start_time'], $validatedData['end_time']])
     ->orWhereBetween('end_time', [$validatedData['start_time'], $validatedData['end_time']])
     ->orWhere(function ($query) use ($validatedData) {
      $query->where('start_time', '<=', $validatedData['start_time'])
       ->where('end_time', '>=', $validatedData['end_time']);
     });
   })
   ->exists();

  if ($exists) {
   return response()->json(['success' => false, 'message' => 'این بازه زمانی مرخصی قبلاً ثبت شده است.'], 422);
  }

  // ایجاد مرخصی
  Vacation::create([
   'doctor_id' => $doctorId,
   'date' => $validatedData['date'],
   'start_time' => $validatedData['start_time'] ?? null,
   'end_time' => $validatedData['end_time'] ?? null,
   'is_full_day' => $request->is_full_day ? 1 : 0,
  ]);

  return response()->json(['success' => true, 'message' => 'مرخصی با موفقیت ثبت شد.']);
 }







 public function update(Request $request, $id)
 {
  $vacation = Vacation::where('id', $id)
   ->where('doctor_id', Auth::guard('doctor')->user()->id ?? Auth::guard('secretary')->user()->doctor_id )
   ->firstOrFail();

  $validatedData = $request->validate([
   'date' => 'required|date',
   'start_time' => 'nullable|date_format:H:i',
   'end_time' => 'nullable|date_format:H:i|after:start_time',
   'is_full_day' => 'nullable|boolean',
  ]);

  $gregorianDate = CalendarUtils::createDatetimeFromFormat('Y/m/d', $request->date)->format('Y-m-d');
  $validatedData['date'] = $gregorianDate;

  if ($request->is_full_day) {
   $validatedData['start_time'] = '00:00';
   $validatedData['end_time'] = '23:00';
  }

  $exists = Vacation::where('doctor_id', $vacation->doctor_id)
   ->where('date', $validatedData['date'])
   ->where('id', '!=', $vacation->id)
   ->where(function ($query) use ($validatedData) {
    $query->whereBetween('start_time', [$validatedData['start_time'], $validatedData['end_time']])
     ->orWhereBetween('end_time', [$validatedData['start_time'], $validatedData['end_time']])
     ->orWhere(function ($query) use ($validatedData) {
      $query->where('start_time', '<=', $validatedData['start_time'])
       ->where('end_time', '>=', $validatedData['end_time']);
     });
   })
   ->exists();

  if ($exists) {
   return response()->json(['success' => false, 'message' => 'این بازه زمانی مرخصی قبلاً ثبت شده است.'], 422);
  }

  $vacation->update([
   'date' => $validatedData['date'],
   'start_time' => $validatedData['start_time'] ?? null,
   'end_time' => $validatedData['end_time'] ?? null,
   'is_full_day' => $request->is_full_day ? 1 : 0,
  ]);

  return response()->json(['success' => true, 'message' => 'مرخصی با موفقیت به‌روزرسانی شد.']);
 }







 public function destroy($id)
 {
  $doctorId = Auth::guard('doctor')->user()->id ?? Auth::guard('secretary')->user()->doctor_id ;

  $vacation = Vacation::where('id', $id)->where('doctor_id', $doctorId)->firstOrFail();
  $vacation->delete();

  return response()->json(['success' => true, 'message' => 'مرخصی با موفقیت حذف شد.']);
 }

}