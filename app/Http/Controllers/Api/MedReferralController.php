<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Med;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class MedReferralController extends Controller
{
    /**
     * Display a listing of medical referrals for a med card.
     */
    public function index(string $medNumber): JsonResponse
    {
        $med = Med::where('med_number', $medNumber)->firstOrFail();

        $referrals = $med->referrals()
            ->with(['referringDoctor:id,name,specialty', 'referringClinic:id,name', 'targetClinic:id,name'])
            ->paginate(15);

        return response()->json([
            'status' => 'success',
            'data' => $referrals,
        ]);
    }

    /**
     * Store a new medical referral.
     */
    public function store(Request $request, string $medNumber): JsonResponse
    {
        $med = Med::where('med_number', $medNumber)->firstOrFail();

        $validated = $request->validate([
            'referring_doctor_id' => 'nullable|exists:users,id',
            'referring_clinic_id' => 'nullable|exists:clinics,id',
            'target_clinic_id' => 'required|exists:clinics,id',
            'specialty_needed' => 'required|string|max:100',
            'reason' => 'required|string|max:500',
            'urgency' => 'nullable|in:routine,urgent,emergency',
            'status' => 'nullable|in:pending,accepted,completed,cancelled',
            'expires_at' => 'nullable|date',
        ]);

        $referral = $med->referrals()->create([
            'referring_doctor_id' => $validated['referring_doctor_id'] ?? auth()->id(),
            'referring_clinic_id' => $validated['referring_clinic_id'] ?? null,
            'target_clinic_id' => $validated['target_clinic_id'],
            'specialty_needed' => $validated['specialty_needed'],
            'reason' => $validated['reason'],
            'urgency' => $validated['urgency'] ?? 'routine',
            'status' => $validated['status'] ?? 'pending',
            'expires_at' => $validated['expires_at'] ?? now()->addDays(30),
        ]);

        $med->logAccess(auth()->id(), 'referral_issued', [
            'referral_id' => $referral->id,
            'specialty' => $referral->specialty_needed,
            'target_clinic_id' => $referral->target_clinic_id,
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Tibbiy yo\'llanma muvaffaqiyatli rasmiylashtirildi.',
            'data' => $referral->load(['referringDoctor', 'referringClinic', 'targetClinic']),
        ], 201);
    }
}
