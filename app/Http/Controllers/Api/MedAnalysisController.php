<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Med;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class MedAnalysisController extends Controller
{
    /**
     * Display a listing of laboratory and diagnostic analyses for a med card.
     */
    public function index(string $medNumber): JsonResponse
    {
        $med = Med::where('med_number', $medNumber)->firstOrFail();

        $analyses = $med->analyses()
            ->with(['doctor:id,name,specialty', 'clinic:id,name,type'])
            ->paginate(15);

        return response()->json([
            'status' => 'success',
            'data' => $analyses,
        ]);
    }

    /**
     * Store a new laboratory analysis result.
     */
    public function store(Request $request, string $medNumber): JsonResponse
    {
        $med = Med::where('med_number', $medNumber)->firstOrFail();

        $validated = $request->validate([
            'doctor_id' => 'nullable|exists:users,id',
            'clinic_id' => 'nullable|exists:clinics,id',
            'analysis_type' => 'required|in:blood,biochemistry,urine,mri,ct,ecg,ultrasound',
            'title' => 'required|string|max:255',
            'indicators' => 'nullable|array',
            'conclusion' => 'nullable|string',
            'status' => 'nullable|in:normal,abnormal,critical,pending',
            'attachment_url' => 'nullable|string',
            'performed_at' => 'nullable|date',
        ]);

        $analysis = $med->analyses()->create([
            'doctor_id' => $validated['doctor_id'] ?? auth()->id(),
            'clinic_id' => $validated['clinic_id'] ?? null,
            'analysis_type' => $validated['analysis_type'],
            'title' => $validated['title'],
            'indicators' => $validated['indicators'] ?? [],
            'conclusion' => $validated['conclusion'] ?? null,
            'status' => $validated['status'] ?? 'normal',
            'attachment_url' => $validated['attachment_url'] ?? null,
            'performed_at' => $validated['performed_at'] ?? now(),
        ]);

        $med->logAccess(auth()->id(), 'analysis_uploaded', [
            'analysis_id' => $analysis->id,
            'title' => $analysis->title,
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Laboratoriya tahlili natijasi saqlandi.',
            'data' => $analysis->load(['doctor', 'clinic']),
        ], 201);
    }
}
