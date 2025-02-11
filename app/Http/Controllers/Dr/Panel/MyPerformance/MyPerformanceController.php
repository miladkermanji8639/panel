<?php

namespace App\Http\Controllers\Dr\Panel\MyPerformance;

use App\Models\Dr\Clinic;
use Illuminate\Http\Request;

class MyPerformanceController
{
    public function index()
    {
        // دریافت لیست کلینیک ها  
        $clinics = Clinic::all();

        return view('dr.panel.my-performance.index', ['clinics' => $clinics]);
    }
    public function chart(){
        return view('dr.panel.my-performance.chart.index');
    }
}
