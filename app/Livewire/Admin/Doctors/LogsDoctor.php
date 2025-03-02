<?php
namespace App\Livewire\Admin\Doctors;
use Carbon\Carbon;
use Livewire\Component;
use App\Models\Dr\Doctor;
use Livewire\WithPagination;
use Morilog\Jalali\Jalalian;
use App\Helpers\JalaliHelper;
use App\Models\Dr\Appointment;
use App\Models\Dr\UserBlocking;
use Illuminate\Support\Facades\Log;
class LogsDoctor extends Component
{
    use WithPagination;
    public $reqDoctor = '0'; // مقدار انتخاب‌شده
    public $mobile = '';
    public $trackingCode = '';
    public $startDate = '';
    public $endDate = '';
    public $search = '';
    public $selectedAppointments = [];
    public $selectAll = false;
    public $perPage = 50;
    public function updated($propertyName)
    {
        if (in_array($propertyName, ['reqDoctor', 'mobile', 'trackingCode', 'startDate', 'endDate', 'search', 'selectAll'])) {
            $this->resetPage();
        }
    }
    public function updatedSelectAll($value)
    {
        $appointments = $this->getAppointmentsQuery()->paginate($this->perPage);
        $this->selectedAppointments = $value ? $appointments->pluck('id')->toArray() : [];
    }
    public function updatedSelectedAppointments()
    {
        $appointments = $this->getAppointmentsQuery()->paginate($this->perPage);
        $currentPageIds = $appointments->pluck('id')->toArray();
        $this->selectAll = !empty($this->selectedAppointments) && !array_diff($currentPageIds, $this->selectedAppointments);
    }
    public function resetFilters()
    {
        $this->reqDoctor = '0';
        $this->mobile = '';
        $this->trackingCode = '';
        $this->startDate = '';
        $this->endDate = '';
        $this->search = '';
        $this->selectedAppointments = [];
        $this->selectAll = false;
        $this->resetPage();
    }
    public function banUser($userId, $doctorId, $userName)
    {
        $this->dispatch('showBanForm', userId: $userId, doctorId: $doctorId, userName: $userName);
    }
    public function confirmBanUser($userId, $doctorId, $reason, $expiry)
    {
        try {
            $expiryGregorian = JalaliHelper::parsePersianTextDate($expiry);
            UserBlocking::create([
                'user_id' => $userId,
                'doctor_id' => $doctorId,
                'blocked_at' => now(),
                'unblocked_at' => $expiryGregorian,
                'reason' => $reason,
                'status' => 1
            ]);
            $this->dispatch('toast', 'کاربر با موفقیت مسدود شد.', ['type' => 'success']);
        } catch (\Exception $e) {
            $this->dispatch('toast', 'خطا در مسدود کردن کاربر: ' . $e->getMessage(), ['type' => 'error']);
        }
    }
    public function cancelAppointment($id)
    {
        $this->dispatch('confirmAction', 'cancel', $id);
    }
    public function confirmCancel($id)
    {
        try {
            $appointment = Appointment::findOrFail($id);
            $appointment->update(['status' => 'cancelled']);
            $this->dispatch('toast', 'نوبت با موفقیت لغو شد.', ['type' => 'success']);
        } catch (\Exception $e) {
            $this->dispatch('toast', 'خطا در لغو نوبت: ' . $e->getMessage(), ['type' => 'error']);
        }
    }
    public function deleteAppointment($id)
    {
        $this->dispatch('confirmAction', 'delete', $id);
    }
    public function confirmDelete($id)
    {
        try {
            $appointment = Appointment::findOrFail($id);
            $appointment->delete();
            $this->selectedAppointments = array_diff($this->selectedAppointments, [$id]);
            $this->dispatch('toast', 'نوبت با موفقیت حذف شد.', ['type' => 'success']);
        } catch (\Exception $e) {
            $this->dispatch('toast', 'خطا در حذف نوبت: ' . $e->getMessage(), ['type' => 'error']);
        }
    }
    public function deleteSelected()
    {
        if (empty($this->selectedAppointments)) {
            $this->dispatch('toast', 'هیچ نوبت‌ی انتخاب نشده است.', ['type' => 'warning']);
            return;
        }
        $this->dispatch('confirmDeleteSelected');
    }
    public function confirmDeleteSelected()
    {
        try {
            Appointment::whereIn('id', $this->selectedAppointments)->delete();
            $this->selectedAppointments = [];
            $this->selectAll = false;
            $this->dispatch('toast', 'نوبت‌های انتخاب‌شده با موفقیت حذف شدند.', ['type' => 'success']);
        } catch (\Exception $e) {
            $this->dispatch('toast', 'خطا در حذف نوبت‌ها: ' . $e->getMessage(), ['type' => 'error']);
        }
    }
    public function export()
    {
        $appointments = $this->getAppointmentsQuery()->get();
        $csv = "ردیف,پزشک,شماره تماس,استان/شهر,تاریخ ملاقات,زمان ملاقات,نام کاربر,کدملی کاربر,تاریخ رزرو,کد پیگیری,وضعیت\n";
        foreach ($appointments as $index => $appointment) {
            $csv .= ($index + 1) . ',' .
                ($appointment->doctor->full_name ?? '') . ',' .
                ($appointment->doctor->mobile ?? '') . ',' .
                ($appointment->doctor->province->name ?? '') . '/' . ($appointment->doctor->city->name ?? '') . ',' .
                JalaliHelper::toJalaliDate($appointment->appointment_date) . ',' .
                ($appointment->start_time ?? '') . ',' .
                ($appointment->patient->first_name ?? '') . ' ' . ($appointment->patient->last_name ?? '') . ' (' . ($appointment->patient->mobile ?? '') . '),' .
                ($appointment->patient->national_code ?? '') . ',' .
                JalaliHelper::toJalaliDateTime($appointment->reserved_at) . ',' .
                ($appointment->tracking_code ?? '') . ',' .
                ($appointment->status === 'scheduled' ? 'در انتظار خدمت' : $appointment->status) . "\n";
        }
        return response()->streamDownload(function () use ($csv) {
            echo $csv;
        }, 'appointments-logs.csv');
    }
    private function getAppointmentsQuery()
    {
        $startDateGregorian = $this->startDate ? JalaliHelper::parsePersianTextDate($this->startDate) : null;
        $endDateGregorian = $this->endDate ? JalaliHelper::parsePersianTextDate($this->endDate) : null;
        return Appointment::with(['doctor', 'patient', 'doctor.province', 'doctor.city'])
            ->when($this->reqDoctor && $this->reqDoctor != '0', fn($q) => $q->where('doctor_id', $this->reqDoctor))
            ->when($this->mobile, fn($q) => $q->whereHas('patient', fn($q2) => $q2->where('mobile', 'like', '%' . trim($this->mobile) . '%')))
            ->when($this->trackingCode, fn($q) => $q->where('tracking_code', 'like', '%' . trim($this->trackingCode) . '%'))
            ->when($this->startDate, fn($q) => $q->whereDate('appointment_date', '>=', $startDateGregorian))
            ->when($this->endDate, fn($q) => $q->whereDate('appointment_date', '<=', $endDateGregorian))
            ->when($this->search, fn($q) => $q->where(function ($query) {
                $searchTerm = '%' . trim($this->search) . '%';
                $query->whereHas('doctor', fn($q2) => $q2->where('first_name', 'like', $searchTerm)
                    ->orWhere('last_name', 'like', $searchTerm)
                    ->orWhere('mobile', 'like', $searchTerm))
                    ->orWhereHas('patient', fn($q2) => $q2->where('first_name', 'like', $searchTerm)
                        ->orWhere('last_name', 'like', $searchTerm)
                        ->orWhere('mobile', 'like', $searchTerm)
                        ->orWhere('national_code', 'like', $searchTerm))
                    ->orWhere('tracking_code', 'like', $searchTerm)
                    ->orWhere('appointment_date', 'like', $searchTerm)
                    ->orWhere('start_time', 'like', $searchTerm);
            }));
    }
    public function toggleBlockUser($userId, $doctorId, $userName, $status)
    {
        $this->dispatch('showBanForm', ['userId' => $userId, 'doctorId' => $doctorId, 'userName' => $userName, 'status' => $status]);
    }
    public function toggleBlockUserConfirm($userId, $doctorId, $data)
    {
        $isBlocked = UserBlocking::where('user_id', $userId)
            ->where('doctor_id', $doctorId)
            ->where('status', 1)
            ->exists();
        if ($data['status'] == 1 && $isBlocked) {
            $this->dispatch('toast', 'این کاربر قبلاً مسدود شده است.', ['type' => 'error']);
            return;
        }
        $response = app('App\Http\Controllers\Dr\Panel\Turn\Schedule\ScheduleSetting\BlockingUsers\BlockingUsersController')
            ->updateStatus(new \Illuminate\Http\Request([
                'id' => UserBlocking::where('user_id', $userId)->where('doctor_id', $doctorId)->first()?->id,
                'status' => $data['status'],
                'selectedClinicId' => 'default',
            ]));
        $result = json_decode($response->getContent(), true);
        if ($result['success']) {
            $this->dispatch('toast', $result['message'], ['type' => 'success']);
        } else {
            $this->dispatch('toast', $result['message'], ['type' => 'error']);
        }
    }
    public function mount()
    {
        $this->reqDoctor = '0'; // مقدار اولیه
    }
    public function render()
    {
        $appointments = $this->getAppointmentsQuery()->paginate($this->perPage);
        $doctors = Doctor::all();
        return view('livewire.admin.doctors.logs-doctor', [
            'appointments' => $appointments,
            'doctors' => $doctors,
            'selectedDoctorId' => $this->reqDoctor // برای استفاده توی Blade
        ]);
    }
}