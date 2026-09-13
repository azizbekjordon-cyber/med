<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Med;
use Illuminate\Http\JsonResponse;

class MedEmergencyController extends Controller
{
    /**
     * 103 Tez Tibbiy Yordam va Shoshilinch Triage (QR Scan).
     * Bemor hushsiz yoki favqulodda holatda bo'lganda login talab qilinmaydi.
     */
    public function triage(string $qrToken): JsonResponse
    {
        $med = Med::where('qr_token', $qrToken)
            ->with(['user', 'records' => fn ($q) => $q->latest()->limit(1)])
            ->first();

        if (! $med) {
            return response()->json([
                'status' => 'error',
                'message' => 'Yaroqsiz yoki eskirgan favqulodda QR-kod!',
            ], 404);
        }

        if ($med->status === 'locked' || $med->status === 'archived') {
            return response()->json([
                'status' => 'error',
                'message' => 'Ushbu med-karta bloklangan yoki arxivlangan.',
            ], 403);
        }

        // Tizimda 103 shoshilinch kirishini audit qilish
        $med->logAccess(auth()->id(), 'emergency_qr_view', [
            'mode' => '103_ambulance_triage',
            'qr_token' => substr($qrToken, 0, 10).'...',
            'ip' => request()->ip(),
        ]);

        $triageData = $med->getEmergencyTriageData();

        return response()->json([
            'status' => 'success',
            'mode' => 'EMERGENCY_TRIAGE_ACCESS',
            'message' => '103 Favqulodda tibbiy ma\'lumotlar muvaffaqiyatli yuklandi.',
            'data' => $triageData,
        ]);
    }
}
