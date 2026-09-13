<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Med;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class MedController extends Controller
{
    /**
     * Display a listing of med cards.
     */
    public function index(Request $request): JsonResponse
    {
        $query = Med::with(['user:id,name,phone,pinfl,gender,birth_date'])
            ->withCount(['records', 'prescriptions', 'analyses', 'vaccinations']);

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('med_number', 'like', "%{$search}%")
                    ->orWhereHas('user', function ($uq) use ($search) {
                        $uq->where('name', 'like', "%{$search}%")
                            ->orWhere('pinfl', 'like', "%{$search}%");
                    });
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }

        $meds = $query->paginate(15);

        return response()->json([
            'status' => 'success',
            'data' => $meds,
        ]);
    }

    /**
     * Display the specified med card profile.
     */
    public function show(string $medNumber): JsonResponse
    {
        $med = Med::where('med_number', $medNumber)
            ->with([
                'user',
                'records.doctor',
                'records.clinic',
                'prescriptions.doctor',
                'prescriptions.pharmacy',
                'analyses.clinic',
                'vaccinations.clinic',
                'referrals.referringClinic',
                'referrals.targetClinic',
                'accessLogs.user',
            ])
            ->firstOrFail();

        // Audit log the view
        $med->logAccess(auth()->id(), 'med_profile_view', [
            'med_number' => $med->med_number,
        ]);

        return response()->json([
            'status' => 'success',
            'data' => $med,
        ]);
    }

    /**
     * Issue a new med card (tibbiy karta ochish).
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'user_id' => 'nullable|exists:users,id',
            'patient_name' => 'required_without:user_id|string|max:255',
            'pinfl' => 'required_without:user_id|string|size:14',
            'phone' => 'nullable|string|max:20',
            'card_type' => 'nullable|in:standard,emergency,pediatric,chronic,senior',
            'blood_group' => 'required|in:O+,O-,A+,A-,B+,B-,AB+,AB-',
            'rhesus_factor' => 'required|in:positive,negative',
            'allergies' => 'nullable|array',
            'chronic_diseases' => 'nullable|array',
            'organ_donor' => 'nullable|boolean',
            'emergency_contact_name' => 'nullable|string|max:255',
            'emergency_contact_phone' => 'nullable|string|max:255',
            'emergency_contact_relation' => 'nullable|string|max:255',
            'insurance_company' => 'nullable|string|max:255',
            'insurance_policy_number' => 'nullable|string|max:255',
            'pin_code' => 'nullable|string|min:4|max:6',
        ]);

        // If user_id not provided, find or create patient user
        if (empty($validated['user_id'])) {
            $user = User::firstOrCreate(
                ['pinfl' => $validated['pinfl']],
                [
                    'name' => $validated['patient_name'],
                    'email' => 'med_'.$validated['pinfl'].'@med.uz',
                    'phone' => $validated['phone'] ?? null,
                    'password' => Hash::make(Str::random(12)),
                    'role' => 'patient',
                ]
            );
            $userId = $user->id;
        } else {
            $userId = $validated['user_id'];
        }

        // Generate medical card number: MED-YYYY-RAND-RAND
        $year = date('Y');
        $part1 = str_pad(mt_rand(1000, 9999), 4, '0', STR_PAD_LEFT);
        $part2 = str_pad(mt_rand(1000, 9999), 4, '0', STR_PAD_LEFT);
        $medNumber = "MED-{$year}-{$part1}-{$part2}";

        $med = Med::create([
            'user_id' => $userId,
            'med_number' => $medNumber,
            'card_type' => $validated['card_type'] ?? 'standard',
            'status' => 'active',
            'blood_group' => $validated['blood_group'],
            'rhesus_factor' => $validated['rhesus_factor'],
            'allergies' => $validated['allergies'] ?? [],
            'chronic_diseases' => $validated['chronic_diseases'] ?? [],
            'organ_donor' => $validated['organ_donor'] ?? false,
            'emergency_contact_name' => $validated['emergency_contact_name'] ?? null,
            'emergency_contact_phone' => $validated['emergency_contact_phone'] ?? null,
            'emergency_contact_relation' => $validated['emergency_contact_relation'] ?? null,
            'insurance_company' => $validated['insurance_company'] ?? null,
            'insurance_policy_number' => $validated['insurance_policy_number'] ?? null,
            'qr_token' => 'MED_EMG_'.Str::upper(Str::random(24)),
            'pin_code' => ! empty($validated['pin_code']) ? Hash::make($validated['pin_code']) : null,
            'balance_credits' => 0.00,
            'issued_at' => now(),
            'expires_at' => now()->addYears(10),
        ]);

        $med->logAccess(auth()->id(), 'med_card_issued', [
            'med_number' => $med->med_number,
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Yangi med-karta muvaffaqiyatli rasmiylashtirildi.',
            'data' => $med->load('user'),
        ], 201);
    }

    /**
     * Update clinical parameters on the med card.
     */
    public function update(Request $request, string $medNumber): JsonResponse
    {
        $med = Med::where('med_number', $medNumber)->firstOrFail();

        $validated = $request->validate([
            'blood_group' => 'nullable|in:O+,O-,A+,A-,B+,B-,AB+,AB-',
            'rhesus_factor' => 'nullable|in:positive,negative',
            'allergies' => 'nullable|array',
            'chronic_diseases' => 'nullable|array',
            'organ_donor' => 'nullable|boolean',
            'emergency_contact_name' => 'nullable|string|max:255',
            'emergency_contact_phone' => 'nullable|string|max:255',
            'emergency_contact_relation' => 'nullable|string|max:255',
            'insurance_company' => 'nullable|string|max:255',
            'insurance_policy_number' => 'nullable|string|max:255',
            'status' => 'nullable|in:active,locked,archived',
        ]);

        $med->update(array_filter($validated, fn ($val) => ! is_null($val)));

        $med->logAccess(auth()->id(), 'med_card_updated', [
            'updated_fields' => array_keys($validated),
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Med-karta ma\'lumotlari yangilandi.',
            'data' => $med,
        ]);
    }

    /**
     * Verify patient PIN code for high-privilege operations.
     */
    public function verifyPin(Request $request, string $medNumber): JsonResponse
    {
        $validated = $request->validate([
            'pin' => 'required|string',
        ]);

        $med = Med::where('med_number', $medNumber)->firstOrFail();

        $verified = $med->verifyPin($validated['pin']);

        if (! $verified) {
            $med->logAccess(auth()->id(), 'pin_verification_failed', []);

            return response()->json([
                'status' => 'error',
                'message' => 'Kiritilgan PIN-kod noto\'g\'ri!',
            ], 403);
        }

        $med->logAccess(auth()->id(), 'pin_verification_success', []);

        return response()->json([
            'status' => 'success',
            'message' => 'PIN-kod tasdiqlandi. To\'liq tibbiy ma\'lumotlarga ruxsat berildi.',
            'verified' => true,
        ]);
    }

    /**
     * Regenerate the emergency triage QR token.
     */
    public function regenerateQr(string $medNumber): JsonResponse
    {
        $med = Med::where('med_number', $medNumber)->firstOrFail();
        $newToken = $med->regenerateQrToken();

        $med->logAccess(auth()->id(), 'qr_token_regenerated', []);

        return response()->json([
            'status' => 'success',
            'message' => 'Favqulodda QR-token yangilandi.',
            'qr_token' => $newToken,
        ]);
    }
}
