<?php

use App\Http\Controllers\Api\MedAnalysisController;
use App\Http\Controllers\Api\MedAppointmentController;
use App\Http\Controllers\Api\MedController;
use App\Http\Controllers\Api\MedEmergencyController;
use App\Http\Controllers\Api\MedPrescriptionController;
use App\Http\Controllers\Api\MedRecordController;
use App\Http\Controllers\Api\MedReferralController;
use App\Http\Controllers\Api\MedVaccinationController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| MED Healthcare & Medical Card REST API Routes (v1)
|--------------------------------------------------------------------------
*/

Route::prefix('v1')->group(function () {
    // 103 Tez Tibbiy Yordam - Favqulodda Triage (No-login QR scan)
    Route::get('/emergency/triage/{qrToken}', [MedEmergencyController::class, 'triage']);

    // Med-Karta (Tibbiy Karta / Med Profile) boshqaruvi
    Route::get('/meds', [MedController::class, 'index']);
    Route::post('/meds', [MedController::class, 'store']);
    Route::get('/meds/{medNumber}', [MedController::class, 'show']);
    Route::put('/meds/{medNumber}', [MedController::class, 'update']);
    Route::post('/meds/{medNumber}/verify-pin', [MedController::class, 'verifyPin']);
    Route::post('/meds/{medNumber}/regenerate-qr', [MedController::class, 'regenerateQr']);

    // Tibbiy Ko'riklar, Tashxislar va Kasallik Tarixi (EHR)
    Route::get('/meds/{medNumber}/records', [MedRecordController::class, 'index']);
    Route::post('/meds/{medNumber}/records', [MedRecordController::class, 'store']);
    Route::get('/records/{id}', [MedRecordController::class, 'show']);

    // Elektron Retseptlar (E-Prescriptions)
    Route::get('/meds/{medNumber}/prescriptions', [MedPrescriptionController::class, 'index']);
    Route::post('/meds/{medNumber}/prescriptions', [MedPrescriptionController::class, 'store']);
    Route::post('/prescriptions/{prescriptionNumber}/dispense', [MedPrescriptionController::class, 'dispense']);

    // Laboratoriya va Diagnostika Tahlillari
    Route::get('/meds/{medNumber}/analyses', [MedAnalysisController::class, 'index']);
    Route::post('/meds/{medNumber}/analyses', [MedAnalysisController::class, 'store']);

    // Emlash Jurnali (Vaccinations)
    Route::get('/meds/{medNumber}/vaccinations', [MedVaccinationController::class, 'index']);
    Route::post('/meds/{medNumber}/vaccinations', [MedVaccinationController::class, 'store']);

    // Shifoxona va Mutaxassis Yo'llanmalari (Referrals)
    Route::get('/meds/{medNumber}/referrals', [MedReferralController::class, 'index']);
    Route::post('/meds/{medNumber}/referrals', [MedReferralController::class, 'store']);

    // Onlayn Navbat va Shifokor Qabuli (E-Navbat Appointments)
    Route::get('/meds/{medNumber}/appointments', [MedAppointmentController::class, 'index']);
    Route::get('/appointments/slots', [MedAppointmentController::class, 'slots']);
    Route::post('/appointments/book', [MedAppointmentController::class, 'book']);
    Route::post('/appointments/{id}/cancel', [MedAppointmentController::class, 'cancel']);
});
