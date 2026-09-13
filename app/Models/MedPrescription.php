<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MedPrescription extends Model
{
    use HasFactory;

    protected $fillable = [
        'med_id',
        'med_record_id',
        'doctor_id',
        'prescription_number',
        'medication_name',
        'dosage',
        'frequency',
        'duration_days',
        'instructions',
        'status',
        'dispensed_pharmacy_id',
        'dispensed_at',
    ];

    protected function casts(): array
    {
        return [
            'dispensed_at' => 'datetime',
            'duration_days' => 'integer',
        ];
    }

    public function med(): BelongsTo
    {
        return $this->belongsTo(Med::class);
    }

    public function record(): BelongsTo
    {
        return $this->belongsTo(MedRecord::class, 'med_record_id');
    }

    public function doctor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'doctor_id');
    }

    public function pharmacy(): BelongsTo
    {
        return $this->belongsTo(Clinic::class, 'dispensed_pharmacy_id');
    }

    public function isDispensed(): bool
    {
        return $this->status === 'dispensed';
    }

    public function markAsDispensed(int $pharmacyClinicId): void
    {
        $this->update([
            'status' => 'dispensed',
            'dispensed_pharmacy_id' => $pharmacyClinicId,
            'dispensed_at' => now(),
        ]);
    }
}
