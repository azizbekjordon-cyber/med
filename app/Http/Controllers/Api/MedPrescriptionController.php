<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Med;
use App\Models\MedPrescription;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class MedPrescriptionController extends Controller
{
    /**
     * Display a listing of prescriptions for a med card.
     */
    public function index(string $medNumber): JsonResponse
    {
        $med = Med::where('med_number', $medNumber)->firstOrFail();

        $prescriptions = $med->prescriptions()
            ->with(['doctor:id,name,specialty', 'pharmacy:id,name'])
            ->paginate(15);

        return response()->json([
            'status' => 'success',
            'data' => $prescriptions,
        ]);
    }

    /**
     * Prescribe a new medication.
     */
    public function store(Request $request, string $medNumber): JsonResponse
    {
        $med = Med::where('med_number', $medNumber)->firstOrFail();

        $validated = $request->validate([
            'med_record_id' => 'nullable|exists:med_records,id',
            'doctor_id' => 'nullable|exists:users,id',
            'medication_name' => 'required|string|max:255',
            'dosage' => 'required|string|max:100',
            'frequency' => 'required|string|max:255',
            'duration_days' => 'nullable|integer|min:1|max:365',
            'instructions' => 'nullable|string',
        ]);

        $year = date('Y');
        $code = str_pad(mt_rand(10000, 99999), 5, '0', STR_PAD_LEFT);
        $prescriptionNumber = "RX-{$year}-{$code}";

        $prescription = $med->prescriptions()->create([
            'med_record_id' => $validated['med_record_id'] ?? null,
            'doctor_id' => $validated['doctor_id'] ?? auth()->id(),
            'prescription_number' => $prescriptionNumber,
            'medication_name' => $validated['medication_name'],
            'dosage' => $validated['dosage'],
            'frequency' => $validated['frequency'],
            'duration_days' => $validated['duration_days'] ?? 7,
            'instructions' => $validated['instructions'] ?? null,
            'status' => 'active',
        ]);

        $med->logAccess(auth()->id(), 'prescription_created', [
            'prescription_number' => $prescriptionNumber,
            'medication' => $prescription->medication_name,
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Elektron retsept muvaffaqiyatli rasmiylashtirildi.',
            'data' => $prescription->load('doctor'),
        ], 201);
    }

    /**
     * Pharmacy dispenses the medication by prescription number.
     */
    public function dispense(Request $request, string $prescriptionNumber): JsonResponse
    {
        $prescription = MedPrescription::where('prescription_number', $prescriptionNumber)
            ->with('med')
            ->firstOrFail();

        if ($prescription->status === 'dispensed') {
            return response()->json([
                'status' => 'error',
                'message' => 'Ushbu retsept bo\'yicha dori allaqachon berilgan!',
                'dispensed_at' => $prescription->dispensed_at?->format('d.m.Y H:i'),
            ], 422);
        }

        if ($prescription->status === 'cancelled') {
            return response()->json([
                'status' => 'error',
                'message' => 'Ushbu retsept bekor qilingan.',
            ], 422);
        }

        $validated = $request->validate([
            'pharmacy_clinic_id' => 'nullable|exists:clinics,id',
        ]);

        $prescription->update([
            'status' => 'dispensed',
            'dispensed_pharmacy_id' => $validated['pharmacy_clinic_id'] ?? null,
            'dispensed_at' => now(),
        ]);

        $prescription->med->logAccess(auth()->id(), 'prescription_dispensed', [
            'prescription_number' => $prescription->prescription_number,
            'medication' => $prescription->medication_name,
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Retsept bo\'yicha dori vositasi dorixonada muvaffaqiyatli berildi.',
            'data' => $prescription->fresh(['pharmacy', 'doctor']),
        ]);
    }
}
