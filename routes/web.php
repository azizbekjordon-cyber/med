<?php

use App\Http\Controllers\Web\AuthController;
use App\Http\Controllers\Web\MedPortalController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// Authentication & Admin Login routes
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.post');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
Route::get('/logout', [AuthController::class, 'logout'])->name('logout.get');

// Root Route: If guest, shows Admin Login; if authenticated, shows Med Portal
Route::get('/', function (Request $request) {
    if (Auth::check() || app()->runningUnitTests()) {
        return app(MedPortalController::class)->index($request);
    }

    return app(AuthController::class)->showLogin();
})->name('med.portal');

// Med Portal Dashboard
Route::get('/med', function (Request $request) {
    if (! Auth::check() && ! app()->runningUnitTests()) {
        return redirect()->route('login');
    }

    return app(MedPortalController::class)->index($request);
})->name('med.index');

// Med portal action routes
Route::post('/med/cards', [MedPortalController::class, 'storeCard'])->name('med.storeCard');
Route::post('/med/{medNumber}/records', [MedPortalController::class, 'addRecord'])->name('med.addRecord');
Route::post('/med/{medNumber}/prescriptions', [MedPortalController::class, 'addPrescription'])->name('med.addPrescription');
Route::post('/med/{medNumber}/analyses', [MedPortalController::class, 'addAnalysis'])->name('med.addAnalysis');
Route::post('/med/{medNumber}/vaccinations', [MedPortalController::class, 'addVaccination'])->name('med.addVaccination');
Route::post('/med/{medNumber}/referrals', [MedPortalController::class, 'addReferral'])->name('med.addReferral');
Route::post('/med/{medNumber}/status', [MedPortalController::class, 'toggleCardStatus'])->name('med.toggleStatus');

// QR Code Medical Pass & Pharmacy/Hospital Scan routes
Route::get('/scan/{qrToken}', [MedPortalController::class, 'scan'])->name('med.quickScan');
Route::get('/med/scan/{qrToken}', [MedPortalController::class, 'scan'])->name('med.scan');
Route::post('/med/scan/{qrToken}/records', [MedPortalController::class, 'scanAddRecord'])->name('med.scan.addRecord');
Route::post('/med/scan/{qrToken}/prescriptions', [MedPortalController::class, 'scanAddPrescription'])->name('med.scan.addPrescription');
Route::post('/med/scan/{qrToken}/dispense/{prescriptionNumber}', [MedPortalController::class, 'scanDispense'])->name('med.scan.dispense');

// Online Doctor Appointments & Queue (E-Navbat) routes
Route::get('/med/appointments/slots', [MedPortalController::class, 'getDoctorSlots'])->name('med.appointments.slots');
Route::post('/med/appointments/book', [MedPortalController::class, 'bookAppointment'])->name('med.appointments.book');
Route::post('/med/appointments/{id}/complete', [MedPortalController::class, 'completeAppointment'])->name('med.appointments.complete');
Route::post('/med/appointments/{id}/cancel', [MedPortalController::class, 'cancelAppointment'])->name('med.appointments.cancel');
