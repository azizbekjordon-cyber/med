<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Med;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class MedVaccinationController extends Controller
{
    /**
     * Display a listing of vaccinations for a med card.
     */
    public function index(string $medNumber): JsonResponse
    {
        $med = Med::where('med_number', $medNumber)->firstOrFail();

        $vaccinations = $med->vaccinations()
            ->with(['doctor:id,name,specialty', 'clinic:id,name,type'])
            ->paginate(15);

        return response()->json([
            'status' => 'success',
            'data' => $vaccinations,
        ]);
    }

    /**
     * Store a new vaccination record.
     */
    public function store(Request $request, string $medNumber): JsonResponse
    {
        $med = Med::where('med_number', $medNumber)->firstOrFail();

        $validated = $request->validate([
            'vaccine_name' => 'required|string|max:255',
            'dose_number' => 'required|integer|min:1|max:10',
            'batch_number' => 'nullable|string|max:50',
            'clinic_id' => 'nullable|exists:clinics,id',
            'administered_by_doctor_id' => 'nullable|exists:users,id',
            'administered_at' => 'nullable|date',
            'next_due_date' => 'nullable|date',
            'notes' => 'nullable|string',
        ]);

        $vaccination = $med->vaccinations()->create([
            'vaccine_name' => $validated['vaccine_name'],
            'dose_number' => $validated['dose_number'],
            'batch_number' => $validated['batch_number'] ?? null,
            'clinic_id' => $validated['clinic_id'] ?? null,
            'administered_by_doctor_id' => $validated['administered_by_doctor_id'] ?? auth()->id(),
            'administered_at' => $validated['administered_at'] ?? now(),
            'next_due_date' => $validated['next_due_date'] ?? null,
            'notes' => $validated['notes'] ?? null,
        ]);

        $med->logAccess(auth()->id(), 'vaccination_recorded', [
            'vaccination_id' => $vaccination->id,
            'vaccine_name' => $vaccination->vaccine_name,
            'dose' => $vaccination->dose_number,
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Emlash ma\'lumoti muvaffaqiyatli saqlandi.',
            'data' => $vaccination->load(['doctor', 'clinic']),
        ], 201);
    }
}
