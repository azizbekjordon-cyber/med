<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Med;
use App\Models\MedRecord;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class MedRecordController extends Controller
{
    /**
     * Display a listing of clinical records for a med card.
     */
    public function index(string $medNumber): JsonResponse
    {
        $med = Med::where('med_number', $medNumber)->firstOrFail();

        $records = $med->records()
            ->with(['doctor:id,name,specialty', 'clinic:id,name,type', 'prescriptions'])
            ->paginate(15);

        return response()->json([
            'status' => 'success',
            'data' => $records,
        ]);
    }

    /**
     * Store a new clinical diagnosis / examination record.
     */
    public function store(Request $request, string $medNumber): JsonResponse
    {
        $med = Med::where('med_number', $medNumber)->firstOrFail();

        $validated = $request->validate([
            'doctor_id' => 'nullable|exists:users,id',
            'clinic_id' => 'nullable|exists:clinics,id',
            'visit_type' => 'nullable|in:outpatient,inpatient,emergency,telemed',
            'icd10_code' => 'nullable|string|max:20',
            'diagnosis' => 'required|string|max:500',
            'symptoms' => 'nullable|string',
            'vitals' => 'nullable|array',
            'objective_examination' => 'nullable|string',
            'treatment_plan' => 'nullable|string',
            'visit_date' => 'nullable|date',
        ]);

        $record = $med->records()->create([
            'doctor_id' => $validated['doctor_id'] ?? auth()->id(),
            'clinic_id' => $validated['clinic_id'] ?? null,
            'visit_type' => $validated['visit_type'] ?? 'outpatient',
            'icd10_code' => $validated['icd10_code'] ?? null,
            'diagnosis' => $validated['diagnosis'],
            'symptoms' => $validated['symptoms'] ?? null,
            'vitals' => $validated['vitals'] ?? null,
            'objective_examination' => $validated['objective_examination'] ?? null,
            'treatment_plan' => $validated['treatment_plan'] ?? null,
            'visit_date' => $validated['visit_date'] ?? now(),
        ]);

        $med->logAccess(auth()->id(), 'med_record_created', [
            'record_id' => $record->id,
            'icd10_code' => $record->icd10_code,
            'diagnosis' => $record->diagnosis,
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Tibbiy ko\'rik va tashxis muvaffaqiyatli saqlandi.',
            'data' => $record->load(['doctor', 'clinic']),
        ], 201);
    }

    /**
     * Display a single record.
     */
    public function show(int $id): JsonResponse
    {
        $record = MedRecord::with(['med.user', 'doctor', 'clinic', 'prescriptions'])
            ->findOrFail($id);

        return response()->json([
            'status' => 'success',
            'data' => $record,
        ]);
    }
}
