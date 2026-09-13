<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MedReferral extends Model
{
    use HasFactory;

    protected $fillable = [
        'med_id',
        'referring_doctor_id',
        'referring_clinic_id',
        'target_clinic_id',
        'specialty_needed',
        'reason',
        'urgency',
        'status',
        'expires_at',
    ];

    protected function casts(): array
    {
        return [
            'expires_at' => 'date',
        ];
    }

    public function med(): BelongsTo
    {
        return $this->belongsTo(Med::class);
    }

    public function referringDoctor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'referring_doctor_id');
    }

    public function referringClinic(): BelongsTo
    {
        return $this->belongsTo(Clinic::class, 'referring_clinic_id');
    }

    public function targetClinic(): BelongsTo
    {
        return $this->belongsTo(Clinic::class, 'target_clinic_id');
    }
}
