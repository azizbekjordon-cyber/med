<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class Med extends Model
{
    use HasFactory;

    protected $table = 'meds';

    protected $fillable = [
        'user_id',
        'med_number',
        'card_type',
        'status',
        'blood_group',
        'rhesus_factor',
        'allergies',
        'chronic_diseases',
        'organ_donor',
        'emergency_contact_name',
        'emergency_contact_phone',
        'emergency_contact_relation',
        'insurance_company',
        'insurance_policy_number',
        'insurance_expires_at',
        'qr_token',
        'pin_code',
        'balance_credits',
        'issued_at',
        'expires_at',
    ];

    protected $hidden = [
        'pin_code',
    ];

    protected function casts(): array
    {
        return [
            'allergies' => 'array',
            'chronic_diseases' => 'array',
            'organ_donor' => 'boolean',
            'balance_credits' => 'decimal:2',
            'insurance_expires_at' => 'date',
            'issued_at' => 'date',
            'expires_at' => 'date',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function records(): HasMany
    {
        return $this->hasMany(MedRecord::class)->orderByDesc('visit_date');
    }

    public function prescriptions(): HasMany
    {
        return $this->hasMany(MedPrescription::class)->orderByDesc('created_at');
    }

    public function activePrescriptions(): HasMany
    {
        return $this->hasMany(MedPrescription::class)->where('status', 'active');
    }

    public function analyses(): HasMany
    {
        return $this->hasMany(MedAnalysis::class)->orderByDesc('performed_at');
    }

    public function vaccinations(): HasMany
    {
        return $this->hasMany(MedVaccination::class)->orderByDesc('administered_at');
    }

    public function referrals(): HasMany
    {
        return $this->hasMany(MedReferral::class)->orderByDesc('created_at');
    }

    public function accessLogs(): HasMany
    {
        return $this->hasMany(MedAccessLog::class)->orderByDesc('created_at');
    }

    public function appointments(): HasMany
    {
        return $this->hasMany(DoctorAppointment::class)->orderBy('appointment_date')->orderBy('appointment_time');
    }

    /**
     * Verify the patient PIN code for high-privilege doctor access.
     */
    public function verifyPin(string $pin): bool
    {
        if (empty($this->pin_code)) {
            return true;
        }

        return Hash::check($pin, $this->pin_code);
    }

    /**
     * Generate a new unique emergency QR token for rapid triage.
     */
    public function regenerateQrToken(): string
    {
        $this->qr_token = 'MED_EMG_'.Str::upper(Str::random(24));
        $this->save();

        return $this->qr_token;
    }

    /**
     * Log an access entry for medical privacy and audit trail.
     */
    public function logAccess(?int $userId, string $action, array $details = []): MedAccessLog
    {
        return $this->accessLogs()->create([
            'user_id' => $userId,
            'action' => $action,
            'details' => $details,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);
    }

    /**
     * Fast emergency data for ambulance 103 triage.
     */
    public function getEmergencyTriageData(): array
    {
        return [
            'med_number' => $this->med_number,
            'patient_name' => $this->user?->name ?? 'Noma\'lum fuqaro',
            'birth_date' => $this->user?->birth_date?->format('d.m.Y'),
            'gender' => $this->user?->gender,
            'blood_group' => $this->blood_group,
            'rhesus_factor' => $this->rhesus_factor,
            'allergies' => $this->allergies ?? [],
            'chronic_diseases' => $this->chronic_diseases ?? [],
            'organ_donor' => $this->organ_donor,
            'emergency_contact' => [
                'name' => $this->emergency_contact_name,
                'phone' => $this->emergency_contact_phone,
                'relation' => $this->emergency_contact_relation,
            ],
            'insurance' => [
                'company' => $this->insurance_company,
                'policy_number' => $this->insurance_policy_number,
                'valid' => $this->insurance_expires_at ? $this->insurance_expires_at->isFuture() : false,
            ],
            'active_medications' => $this->activePrescriptions()
                ->select(['medication_name', 'dosage', 'frequency'])
                ->get(),
            'last_recorded_vitals' => $this->records()->first()?->vitals,
        ];
    }

    /**
     * Masked med card number for public display.
     */
    public function getMaskedNumberAttribute(): string
    {
        $parts = explode('-', $this->med_number);
        if (count($parts) === 4) {
            return "MED-••••-••••-{$parts[3]}";
        }

        return substr($this->med_number, 0, 4).'••••'.substr($this->med_number, -4);
    }
}
