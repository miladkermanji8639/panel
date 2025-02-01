<?php

use Illuminate\Support\Facades\Route;
use Modules\Payment\App\Http\Controllers\PaymentController;
use App\Http\Controllers\Dr\Panel\Profile\DrUpgradeProfileController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::middleware(['web', 'doctor'])->prefix('payment')->group(function () {
    Route::post('/pay', [DrUpgradeProfileController::class, 'payForUpgrade'])->name('doctor.upgrade.pay');
    Route::get('/callback', [DrUpgradeProfileController::class, 'paymentCallback'])->name('doctor.upgrade.callback');
});

// مسیرهای مربوط به ارتقاء پزشک
Route::middleware(['web', 'doctor'])->group(function () {
    Route::get('/doctor/upgrade', [DrUpgradeProfileController::class, 'index'])->name('doctor.upgrade');
});

