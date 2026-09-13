<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Clinic;
use App\Models\DoctorAppointment;
use App\Models\Med;
use App\Models\MedAnalysis;
use App\Models\MedPrescription;
use App\Models\MedRecord;
use App\Models\User;
use Database\Seeders\MedicalSystemSeeder;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class MedPortalController extends Controller
{
    /**
     * Med Portal Dashboard view.
     */
    public function index(Request $request): View
    {
        // 1. Agar bazada med kartalar mavjud bo'lmasa (masalan yangi server / Railway),
        // tizimni avtomatik ravishda boshlang'ich ma'lumotlar bilan to'ldiramiz.
        if (Med::count() === 0) {
            try {
                (new MedicalSystemSeeder)->run();
            } catch (\Throwable $e) {
                Log::warning('Medical system auto-seeding failed: '.$e->getMessage());
            }
        }

        $currentUser = Auth::user();

        // 2. Agar foydalanuvchi tizimga kirgan bo'lsa va unda karta yo'q bo'lsa
        if ($currentUser) {
            $userHasMed = Med::where('user_id', $currentUser->id)->exists();
            if (! $userHasMed) {
                $primaryMed = Med::where('med_number', 'MED-2026-7841-9012')->first();
                if ($primaryMed && (empty($primaryMed->user_id) || $primaryMed->user_id !== $currentUser->id)) {
                    $primaryMed->user_id = $currentUser->id;
                    $primaryMed->save();
                }
            }
        }

        $selectedMedNumber = $request->query('med');

        $activeMed = null;
        if (! empty($selectedMedNumber)) {
            $activeMed = Med::where('med_number', $selectedMedNumber)->first();
        }

        if (! $activeMed && $currentUser) {
            $activeMed = Med::where('user_id', $currentUser->id)->first();
        }

        if (! $activeMed) {
            $activeMed = Med::where('med_number', 'MED-2026-7841-9012')->first() ?? Med::first();
        }

        // 3. Favqulodda himoya: Agar bazada hali ham karta topilmasa, kafolatlangan karta yaratamiz
        if (! $activeMed) {
            $ownerId = $currentUser?->id ?? User::first()?->id ?? User::create([
                'name' => 'Bemor',
                'email' => 'patient_'.Str::random(6).'@med.uz',
                'password' => Hash::make('password123'),
                'role' => 'patient',
            ])->id;

            $activeMed = Med::create([
                'user_id' => $ownerId,
                'med_number' => 'MED-2026-7841-9012',
                'card_type' => 'standard',
                'status' => 'active',
                'blood_group' => 'A(II)+',
                'rhesus_factor' => 'positive',
                'qr_token' => 'MED_EMG_'.strtoupper(Str::random(20)),
                'pin_code' => Hash::make('1234'),
                'issued_at' => now(),
                'expires_at' => now()->addYears(10),
            ]);
        }

        $activeMed->loadMissing([
            'user',
            'records.doctor',
            'records.clinic',
            'prescriptions.doctor',
            'prescriptions.pharmacy',
            'analyses.doctor',
            'analyses.clinic',
            'vaccinations.doctor',
            'vaccinations.clinic',
            'referrals.referringDoctor',
            'referrals.referringClinic',
            'referrals.targetClinic',
            'accessLogs.user',
            'appointments.doctor.clinic',
            'appointments.clinic',
        ]);

        $allMeds = Med::with('user:id,name,phone,pinfl')
            ->orderBy('id')
            ->get();

        $clinics = Clinic::where('is_active', true)->get();
        $doctors = User::where('role', 'doctor')->with('clinic')->get();

        $stats = [
            'total_meds' => Med::count(),
            'active_records' => MedRecord::count(),
            'active_prescriptions' => MedPrescription::where('status', 'active')->count(),
            'completed_analyses' => MedAnalysis::count(),
            'total_appointments' => DoctorAppointment::where('status', 'pending')->count(),
        ];

        return view('med.index', compact(
            'activeMed',
            'allMeds',
            'clinics',
            'doctors',
            'stats'
        ));
    }

    /**
     * Issue new med card from web interface.
     */
    public function storeCard(Request $request): RedirectResponse
    {
        if (Auth::check() && Auth::user()->isPatient()) {
            return redirect()->back()->with('warning', 'Bemorlar uchun yangi karta ochish cheklangan.');
        }

        $validated = $request->validate([
            'patient_name' => 'required|string|max:255',
            'pinfl' => 'required|string|size:14',
            'phone' => 'nullable|string',
            'card_type' => 'required|in:standard,pediatric,chronic,senior',
            'blood_group' => 'required|in:O+,O-,A+,A-,B+,B-,AB+,AB-',
            'rhesus_factor' => 'required|in:positive,negative',
            'allergies_raw' => 'nullable|string',
            'chronic_raw' => 'nullable|string',
            'emergency_contact_name' => 'nullable|string',
            'emergency_contact_phone' => 'nullable|string',
        ]);

        $user = User::firstOrCreate(
            ['pinfl' => $validated['pinfl']],
            [
                'name' => $validated['patient_name'],
                'email' => 'med_'.$validated['pinfl'].'@med.uz',
                'phone' => $validated['phone'],
                'password' => bcrypt('password123'),
                'role' => 'patient',
            ]
        );

        $allergies = ! empty($validated['allergies_raw'])
            ? array_filter(array_map('trim', explode(',', $validated['allergies_raw'])))
            : [];

        $chronic = ! empty($validated['chronic_raw'])
            ? array_filter(array_map('trim', explode(',', $validated['chronic_raw'])))
            : [];

        $year = date('Y');
        $part1 = str_pad(mt_rand(1000, 9999), 4, '0', STR_PAD_LEFT);
        $part2 = str_pad(mt_rand(1000, 9999), 4, '0', STR_PAD_LEFT);
        $medNumber = "MED-{$year}-{$part1}-{$part2}";

        $med = Med::create([
            'user_id' => $user->id,
            'med_number' => $medNumber,
            'card_type' => $validated['card_type'],
            'status' => 'active',
            'blood_group' => $validated['blood_group'],
            'rhesus_factor' => $validated['rhesus_factor'],
            'allergies' => array_values($allergies),
            'chronic_diseases' => array_values($chronic),
            'emergency_contact_name' => $validated['emergency_contact_name'],
            'emergency_contact_phone' => $validated['emergency_contact_phone'],
            'qr_token' => 'MED_EMG_'.strtoupper(Str::random(24)),
            'pin_code' => bcrypt('1234'),
            'balance_credits' => 500000.00,
            'issued_at' => now(),
            'expires_at' => now()->addYears(10),
        ]);

        $med->logAccess(null, 'med_card_issued_web', ['med_number' => $medNumber]);

        return redirect()->route('med.portal', ['med' => $medNumber])
            ->with('success', "Yangi 'Med' karta ({$medNumber}) muvaffaqiyatli ochildi!");
    }

    /**
     * Add clinical diagnosis / record from web interface.
     */
    public function addRecord(Request $request, string $medNumber): RedirectResponse
    {
        if (Auth::check() && Auth::user()->isPatient()) {
            return redirect()->back()->with('warning', "Bemorlar uchun ko'rik va qon bosimini kiritish cheklangan.");
        }

        $med = Med::where('med_number', $medNumber)->firstOrFail();

        $validated = $request->validate([
            'doctor_id' => 'required|exists:users,id',
            'clinic_id' => 'required|exists:clinics,id',
            'icd10_code' => 'nullable|string',
            'diagnosis' => 'required|string',
            'symptoms' => 'nullable|string',
            'treatment_plan' => 'nullable|string',
            'blood_pressure' => 'nullable|string',
            'heart_rate' => 'nullable|string',
            'temperature' => 'nullable|string',
        ]);

        $vitals = [];
        if (! empty($validated['blood_pressure'])) {
            $vitals['blood_pressure'] = $validated['blood_pressure'];
        }
        if (! empty($validated['heart_rate'])) {
            $vitals['heart_rate'] = $validated['heart_rate'].' bpm';
        }
        if (! empty($validated['temperature'])) {
            $vitals['temperature'] = $validated['temperature'].' °C';
        }

        $med->records()->create([
            'doctor_id' => $validated['doctor_id'],
            'clinic_id' => $validated['clinic_id'],
            'visit_type' => 'outpatient',
            'icd10_code' => $validated['icd10_code'],
            'diagnosis' => $validated['diagnosis'],
            'symptoms' => $validated['symptoms'],
            'treatment_plan' => $validated['treatment_plan'],
            'vitals' => $vitals,
            'visit_date' => now(),
        ]);

        $med->logAccess(null, 'doctor_examination_added', [
            'diagnosis' => $validated['diagnosis'],
        ]);

        return redirect()->route('med.portal', ['med' => $medNumber])
            ->with('success', 'Yangi tashxis va ko\'rik qaydi muvaffaqiyatli qo\'shildi.');
    }

    /**
     * Add e-prescription from web interface.
     */
    public function addPrescription(Request $request, string $medNumber): RedirectResponse
    {
        if (Auth::check() && Auth::user()->isPatient()) {
            return redirect()->back()->with('warning', 'Bemorlar uchun retsept va dori yozish cheklangan.');
        }

        $med = Med::where('med_number', $medNumber)->firstOrFail();

        $validated = $request->validate([
            'doctor_id' => 'required|exists:users,id',
            'medication_name' => 'required|string|max:255',
            'dosage' => 'required|string',
            'frequency' => 'required|string',
            'duration_days' => 'required|integer|min:1',
            'instructions' => 'nullable|string',
        ]);

        $code = str_pad(mt_rand(10000, 99999), 5, '0', STR_PAD_LEFT);
        $prescriptionNumber = 'RX-'.date('Y')."-{$code}";

        $med->prescriptions()->create([
            'doctor_id' => $validated['doctor_id'],
            'prescription_number' => $prescriptionNumber,
            'medication_name' => $validated['medication_name'],
            'dosage' => $validated['dosage'],
            'frequency' => $validated['frequency'],
            'duration_days' => $validated['duration_days'],
            'instructions' => $validated['instructions'],
            'status' => 'active',
        ]);

        $med->logAccess(null, 'prescription_added_web', [
            'prescription_number' => $prescriptionNumber,
        ]);

        return redirect()->route('med.portal', ['med' => $medNumber])
            ->with('success', "Yangi elektron retsept ({$prescriptionNumber}) berildi.");
    }

    /**
     * Display patient's Medical Pass & QR Scan Portal for Hospital and Pharmacy.
     */
    public function scan(Request $request, string $qrToken): View
    {
        $med = Med::where('qr_token', $qrToken)
            ->orWhere('med_number', $qrToken)
            ->with([
                'user',
                'records.doctor',
                'records.clinic',
                'prescriptions.doctor',
                'prescriptions.pharmacy',
                'analyses.doctor',
                'analyses.clinic',
                'vaccinations.doctor',
                'vaccinations.clinic',
                'referrals.referringDoctor',
                'referrals.targetClinic',
            ])
            ->firstOrFail();

        $med->logAccess(auth()->id() ?? null, 'qr_scan_verification', [
            'ip' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'active_tab' => $request->query('tab', 'hospital'),
        ]);

        $doctors = User::where('role', 'doctor')->with('clinic')->get();
        $clinics = Clinic::where('is_active', true)->get();
        $pharmacies = Clinic::where('type', 'pharmacy')->where('is_active', true)->get();
        if ($pharmacies->isEmpty()) {
            $pharmacies = Clinic::where('is_active', true)->get();
        }

        $activePrescriptions = $med->prescriptions->where('status', 'active');
        $dispensedPrescriptions = $med->prescriptions->where('status', 'dispensed');

        return view('med.scan', compact(
            'med',
            'doctors',
            'clinics',
            'pharmacies',
            'activePrescriptions',
            'dispensedPrescriptions'
        ));
    }

    /**
     * Add new clinical record via QR scan page (doctor consultation).
     */
    public function scanAddRecord(Request $request, string $qrToken): RedirectResponse
    {
        $med = Med::where('qr_token', $qrToken)->orWhere('med_number', $qrToken)->firstOrFail();

        $validated = $request->validate([
            'doctor_id' => 'required|exists:users,id',
            'clinic_id' => 'required|exists:clinics,id',
            'diagnosis' => 'required|string|max:500',
            'icd10_code' => 'nullable|string|max:20',
            'symptoms' => 'nullable|string',
            'treatment_plan' => 'nullable|string',
            'blood_pressure' => 'nullable|string|max:30',
            'heart_rate' => 'nullable|string|max:20',
            'temperature' => 'nullable|string|max:20',
        ]);

        $vitals = [];
        if (! empty($validated['blood_pressure'])) {
            $vitals['blood_pressure'] = $validated['blood_pressure'];
        }
        if (! empty($validated['heart_rate'])) {
            $vitals['heart_rate'] = $validated['heart_rate'].' bpm';
        }
        if (! empty($validated['temperature'])) {
            $vitals['temperature'] = $validated['temperature'].' °C';
        }

        $med->records()->create([
            'doctor_id' => $validated['doctor_id'],
            'clinic_id' => $validated['clinic_id'],
            'visit_type' => 'hospital_qr_scan',
            'icd10_code' => $validated['icd10_code'] ?? null,
            'diagnosis' => $validated['diagnosis'],
            'symptoms' => $validated['symptoms'] ?? null,
            'treatment_plan' => $validated['treatment_plan'] ?? null,
            'vitals' => $vitals,
            'visit_date' => now(),
        ]);

        $med->logAccess(null, 'doctor_record_added_via_qr_scan', [
            'diagnosis' => $validated['diagnosis'],
        ]);

        return redirect()->route('med.scan', ['qrToken' => $med->qr_token, 'tab' => 'hospital'])
            ->with('success', 'Yangi ko\'rik va tashxis muvaffaqiyatli saqlandi.');
    }

    /**
     * Add prescription from doctor via QR scan page.
     */
    public function scanAddPrescription(Request $request, string $qrToken): RedirectResponse
    {
        $med = Med::where('qr_token', $qrToken)->orWhere('med_number', $qrToken)->firstOrFail();

        $validated = $request->validate([
            'doctor_id' => 'required|exists:users,id',
            'medication_name' => 'required|string|max:255',
            'dosage' => 'required|string|max:100',
            'frequency' => 'required|string|max:255',
            'duration_days' => 'required|integer|min:1|max:365',
            'instructions' => 'nullable|string',
        ]);

        $code = str_pad(mt_rand(10000, 99999), 5, '0', STR_PAD_LEFT);
        $prescriptionNumber = 'RX-'.date('Y')."-{$code}";

        $med->prescriptions()->create([
            'doctor_id' => $validated['doctor_id'],
            'prescription_number' => $prescriptionNumber,
            'medication_name' => $validated['medication_name'],
            'dosage' => $validated['dosage'],
            'frequency' => $validated['frequency'],
            'duration_days' => $validated['duration_days'],
            'instructions' => $validated['instructions'],
            'status' => 'active',
        ]);

        $med->logAccess(null, 'prescription_added_via_qr_scan', [
            'prescription_number' => $prescriptionNumber,
            'medication_name' => $validated['medication_name'],
        ]);

        return redirect()->route('med.scan', ['qrToken' => $med->qr_token, 'tab' => 'prescriptions'])
            ->with('success', "Yangi elektron retsept ({$prescriptionNumber}) kiritildi. Ushbu dori endi dorixonada berish uchun tayyor!");
    }

    /**
     * Dispense medication at pharmacy via QR scan page.
     */
    public function scanDispense(Request $request, string $qrToken, string $prescriptionNumber): RedirectResponse
    {
        $med = Med::where('qr_token', $qrToken)->orWhere('med_number', $qrToken)->firstOrFail();

        $prescription = $med->prescriptions()->where('prescription_number', $prescriptionNumber)->firstOrFail();

        if ($prescription->isDispensed()) {
            return redirect()->route('med.scan', ['qrToken' => $med->qr_token, 'tab' => 'pharmacy'])
                ->with('warning', "Ushbu retsept bo'yicha dori allaqachon berilgan!");
        }

        $pharmacyId = $request->input('pharmacy_id');
        if (! $pharmacyId) {
            $pharmacy = Clinic::where('type', 'pharmacy')->first() ?? Clinic::first();
            $pharmacyId = $pharmacy?->id ?? 1;
        }

        $prescription->markAsDispensed((int) $pharmacyId);

        $med->logAccess(null, 'prescription_dispensed_via_qr', [
            'prescription_number' => $prescriptionNumber,
            'pharmacy_id' => $pharmacyId,
        ]);

        return redirect()->route('med.scan', ['qrToken' => $med->qr_token, 'tab' => 'pharmacy'])
            ->with('success', "Retsept ({$prescriptionNumber} - {$prescription->medication_name}) bo'yicha dori berildi va elektron tizimda muvaffaqiyatli qayd etildi!");
    }

    /**
     * Get available time slots and current queue for a doctor on a specific date.
     */
    public function getDoctorSlots(Request $request): JsonResponse
    {
        $request->validate([
            'doctor_id' => 'required|exists:users,id',
            'date' => 'required|date',
        ]);

        $doctor = User::where('id', $request->input('doctor_id'))
            ->where('role', 'doctor')
            ->with('clinic')
            ->firstOrFail();

        $date = $request->input('date');

        // Standard consultation slots (09:00 - 17:00, with 30 min duration)
        $standardSlots = [
            '09:00', '09:30', '10:00', '10:30', '11:00', '11:30',
            '12:00', '12:30', '14:00', '14:30', '15:00', '15:30', '16:00', '16:30',
        ];

        // Fetch booked appointments on that date
        $booked = DoctorAppointment::where('doctor_id', $doctor->id)
            ->whereDate('appointment_date', $date)
            ->where('status', '!=', 'cancelled')
            ->pluck('queue_number', 'appointment_time')
            ->toArray();

        $slots = [];
        foreach ($standardSlots as $time) {
            $isBooked = isset($booked[$time]);
            $slots[] = [
                'time' => $time,
                'available' => ! $isBooked,
                'queue_number' => $isBooked ? $booked[$time] : null,
            ];
        }

        $nextQueueNumber = count($booked) + 1;

        return response()->json([
            'status' => 'success',
            'doctor' => [
                'id' => $doctor->id,
                'name' => $doctor->name,
                'specialty' => $doctor->specialty ?? 'Mutaxassis',
                'clinic_name' => $doctor->clinic?->name ?? 'Markaziy Shifoxona',
                'room_number' => ($doctor->id * 102).' - xona',
            ],
            'date' => $date,
            'total_booked' => count($booked),
            'next_queue_number' => $nextQueueNumber,
            'slots' => $slots,
        ]);
    }

    /**
     * Book an online appointment with doctor and generate E-Talon.
     */
    public function bookAppointment(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'med_id' => 'required|exists:meds,id',
            'doctor_id' => 'required|exists:users,id',
            'appointment_date' => 'required|date|after_or_equal:today',
            'appointment_time' => 'required|string|max:10',
            'reason' => 'nullable|string|max:500',
        ]);

        $med = Med::with('user')->findOrFail($validated['med_id']);
        $doctor = User::where('id', $validated['doctor_id'])
            ->where('role', 'doctor')
            ->with('clinic')
            ->firstOrFail();

        // Check if slot is already occupied
        $existing = DoctorAppointment::where('doctor_id', $doctor->id)
            ->whereDate('appointment_date', $validated['appointment_date'])
            ->where('appointment_time', $validated['appointment_time'])
            ->where('status', '!=', 'cancelled')
            ->first();

        if ($existing) {
            return redirect()->back()
                ->with('warning', "Kechirasiz, {$doctor->name} shifokorining soat {$validated['appointment_time']} dagi vaqti allaqachon band qilingan. Iltimos, boshqa bo'sh vaqtni tanlang.");
        }

        // Calculate next queue number for this doctor on this date
        $currentQueueCount = DoctorAppointment::where('doctor_id', $doctor->id)
            ->whereDate('appointment_date', $validated['appointment_date'])
            ->where('status', '!=', 'cancelled')
            ->count();

        $queueNumber = $currentQueueCount + 1;
        $ticketNumber = 'NAV-'.date('Y').'-'.str_pad((string) $queueNumber, 3, '0', STR_PAD_LEFT);
        $roomNumber = ($doctor->id * 102).' - xona';

        DoctorAppointment::create([
            'med_id' => $med->id,
            'patient_id' => $med->user_id,
            'doctor_id' => $doctor->id,
            'clinic_id' => $doctor->clinic_id ?? 1,
            'appointment_date' => $validated['appointment_date'],
            'appointment_time' => $validated['appointment_time'],
            'queue_number' => $queueNumber,
            'ticket_number' => $ticketNumber,
            'room_number' => $roomNumber,
            'reason' => $validated['reason'] ?? 'Shifokor ko\'rigi va maslahati',
            'status' => 'pending',
        ]);

        $med->logAccess(null, 'online_appointment_booked', [
            'ticket_number' => $ticketNumber,
            'queue_number' => $queueNumber,
            'doctor' => $doctor->name,
            'date' => $validated['appointment_date'],
            'time' => $validated['appointment_time'],
        ]);

        return redirect()->route('med.portal', ['med' => $med->med_number, 'tab' => 'tabAppointments'])
            ->with('appointment_success', [
                'ticket_number' => $ticketNumber,
                'queue_number' => $queueNumber,
                'doctor_name' => $doctor->name,
                'specialty' => $doctor->specialty,
                'clinic_name' => $doctor->clinic?->name ?? 'Markaziy Klinika',
                'room_number' => $roomNumber,
                'date' => $validated['appointment_date'],
                'time' => $validated['appointment_time'],
                'patient_name' => $med->user->name,
            ])
            ->with('success', "Navbat muvaffaqiyatli olindi! Sizning navbat raqamingiz: № {$queueNumber} ({$ticketNumber}). Qabul vaqti: {$validated['appointment_time']}.");
    }

    /**
     * Cancel an existing appointment.
     */
    public function cancelAppointment(Request $request, int $id): RedirectResponse
    {
        $appointment = DoctorAppointment::with('med')->findOrFail($id);
        $appointment->cancel();

        return redirect()->back()
            ->with('success', "Talon ({$appointment->ticket_number}) bo'yicha navbat bekor qilindi.");
    }

    /**
     * Mark an appointment as completed by doctor.
     */
    public function completeAppointment(Request $request, int $id): RedirectResponse
    {
        if (Auth::check() && Auth::user()->isPatient()) {
            return redirect()->back()->with('warning', 'Qabulni yakunlash faqat shifokorlar uchun ruxsat etilgan.');
        }

        $appointment = DoctorAppointment::with('med')->findOrFail($id);
        $appointment->complete();

        $appointment->med->logAccess(auth()->id() ?? null, 'appointment_completed', [
            'ticket_number' => $appointment->ticket_number,
        ]);

        return redirect()->back()
            ->with('success', "Talon ({$appointment->ticket_number}) bo'yicha qabul muvaffaqiyatli yakunlandi.");
    }

    /**
     * Add clinical analysis result from web interface.
     */
    public function addAnalysis(Request $request, string $medNumber): RedirectResponse
    {
        if (Auth::check() && Auth::user()->isPatient()) {
            return redirect()->back()->with('warning', 'Bemorlar uchun laboratoriya tahlillarini kiritish cheklangan.');
        }

        $med = Med::where('med_number', $medNumber)->firstOrFail();

        $validated = $request->validate([
            'doctor_id' => 'required|exists:users,id',
            'clinic_id' => 'required|exists:clinics,id',
            'analysis_type' => 'required|in:blood,biochemistry,urine,mri,ct,ecg,ultrasound',
            'title' => 'required|string|max:255',
            'indicators_raw' => 'nullable|string',
            'conclusion' => 'nullable|string',
            'status' => 'required|in:normal,abnormal,critical,pending',
        ]);

        $indicators = [];
        if (! empty($validated['indicators_raw'])) {
            $lines = explode("\n", str_replace("\r", '', $validated['indicators_raw']));
            foreach ($lines as $line) {
                $line = trim($line);
                if (empty($line)) {
                    continue;
                }
                $parts = explode(':', $line, 2);
                if (count($parts) === 2) {
                    $indicators[] = [
                        'name' => trim($parts[0]),
                        'value' => trim($parts[1]),
                        'unit' => '',
                        'reference' => 'Me\'yorda',
                        'status' => 'normal',
                    ];
                } else {
                    $indicators[] = [
                        'name' => $line,
                        'value' => 'Ko\'rsatkich',
                        'unit' => '',
                        'reference' => '',
                        'status' => 'normal',
                    ];
                }
            }
        }

        $med->analyses()->create([
            'doctor_id' => $validated['doctor_id'],
            'clinic_id' => $validated['clinic_id'],
            'analysis_type' => $validated['analysis_type'],
            'title' => $validated['title'],
            'indicators' => $indicators,
            'conclusion' => $validated['conclusion'] ?? null,
            'status' => $validated['status'],
            'performed_at' => now(),
        ]);

        $med->logAccess(null, 'analysis_added_web', [
            'analysis_title' => $validated['title'],
        ]);

        return redirect()->route('med.portal', ['med' => $medNumber, 'tab' => 'tabAnalyses'])
            ->with('success', "Yangi laboratoriya/diagnostika tahlili ({$validated['title']}) muvaffaqiyatli saqlandi.");
    }

    /**
     * Add vaccination entry from web interface.
     */
    public function addVaccination(Request $request, string $medNumber): RedirectResponse
    {
        if (Auth::check() && Auth::user()->isPatient()) {
            return redirect()->back()->with('warning', 'Bemorlar uchun emlash qaydlarini kiritish cheklangan.');
        }

        $med = Med::where('med_number', $medNumber)->firstOrFail();

        $validated = $request->validate([
            'administered_by_doctor_id' => 'required|exists:users,id',
            'clinic_id' => 'required|exists:clinics,id',
            'vaccine_name' => 'required|string|max:255',
            'dose_number' => 'required|integer|min:1|max:10',
            'batch_number' => 'nullable|string|max:50',
            'administered_at' => 'required|date',
            'next_due_date' => 'nullable|date',
            'notes' => 'nullable|string',
        ]);

        $med->vaccinations()->create([
            'administered_by_doctor_id' => $validated['administered_by_doctor_id'],
            'clinic_id' => $validated['clinic_id'],
            'vaccine_name' => $validated['vaccine_name'],
            'dose_number' => $validated['dose_number'],
            'batch_number' => $validated['batch_number'] ?? 'VAC-'.mt_rand(10000, 99999),
            'administered_at' => $validated['administered_at'],
            'next_due_date' => $validated['next_due_date'] ?? null,
            'notes' => $validated['notes'] ?? 'Asoratsiz o\'tgan',
        ]);

        $med->logAccess(null, 'vaccination_added_web', [
            'vaccine_name' => $validated['vaccine_name'],
            'dose_number' => $validated['dose_number'],
        ]);

        return redirect()->route('med.portal', ['med' => $medNumber, 'tab' => 'tabVaccinations'])
            ->with('success', "Emlash qaydi ({$validated['vaccine_name']}, {$validated['dose_number']}-doza) muvaffaqiyatli saqlandi.");
    }

    /**
     * Issue medical referral to specialist or clinic.
     */
    public function addReferral(Request $request, string $medNumber): RedirectResponse
    {
        if (Auth::check() && Auth::user()->isPatient()) {
            return redirect()->back()->with('warning', "Bemorlar uchun yo'llanma berish cheklangan.");
        }

        $med = Med::where('med_number', $medNumber)->firstOrFail();

        $validated = $request->validate([
            'referring_doctor_id' => 'required|exists:users,id',
            'referring_clinic_id' => 'required|exists:clinics,id',
            'target_clinic_id' => 'required|exists:clinics,id',
            'specialty_needed' => 'required|string|max:100',
            'reason' => 'required|string|max:500',
            'urgency' => 'required|in:routine,urgent,emergency',
            'expires_days' => 'nullable|integer|min:1|max:90',
        ]);

        $expiresAt = now()->addDays($validated['expires_days'] ?? 30);

        $med->referrals()->create([
            'referring_doctor_id' => $validated['referring_doctor_id'],
            'referring_clinic_id' => $validated['referring_clinic_id'],
            'target_clinic_id' => $validated['target_clinic_id'],
            'specialty_needed' => $validated['specialty_needed'],
            'reason' => $validated['reason'],
            'urgency' => $validated['urgency'],
            'status' => 'pending',
            'expires_at' => $expiresAt,
        ]);

        $med->logAccess(null, 'referral_issued_web', [
            'specialty' => $validated['specialty_needed'],
            'target_clinic_id' => $validated['target_clinic_id'],
        ]);

        return redirect()->route('med.portal', ['med' => $medNumber, 'tab' => 'tabReferrals'])
            ->with('success', "Yo'llanma ({$validated['specialty_needed']} mutaxassisiga) muvaffaqiyatli rasmiylashtirildi.");
    }

    /**
     * Lock or unlock the medical card for emergency or security reasons.
     */
    public function toggleCardStatus(Request $request, string $medNumber): RedirectResponse
    {
        if (Auth::check() && Auth::user()->isPatient()) {
            return redirect()->back()->with('warning', "Karta holatini o'zgartirish bemorlar uchun cheklangan.");
        }

        $med = Med::where('med_number', $medNumber)->firstOrFail();

        $newStatus = $med->status === 'active' ? 'locked' : 'active';
        $med->update(['status' => $newStatus]);

        $med->logAccess(auth()->id() ?? null, 'card_status_toggled', [
            'new_status' => $newStatus,
            'ip' => $request->ip(),
        ]);

        $msg = $newStatus === 'locked'
            ? "Med-karta ({$medNumber}) xavfsizlik maqsadida BLOKLANDI!"
            : "Med-karta ({$medNumber}) muvaffaqiyatli FAOLLASHTIRILDI!";

        return redirect()->route('med.portal', ['med' => $medNumber])
            ->with($newStatus === 'locked' ? 'warning' : 'success', $msg);
    }
}
