<?php

namespace App\Http\Controllers\Dr\Panel\DoctorsClinic\Activation\Workhours;

use App\Models\Dr\DoctorAppointmentConfig;
use Auth;
use Illuminate\Http\Request;

class ActivationWorkhoursController
{
    public function index($clinicId)
    {
        $doctorId = Auth::guard('doctor')->user()->id;
        $otherSite = DoctorAppointmentConfig::where('collaboration_with_other_sites',1)->first();
        return view('dr.panel.doctors-clinic.activation.workhours.index', compact(['clinicId', 'doctorId','otherSite']));
    }
}
