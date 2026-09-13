<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class MedRecord extends Model
{
    use HasFactory;

    protected $fillable = [
        'med_id',
        'doctor_id',
        'clinic_id',
        'visit_type',
        'icd10_code',
        'diagnosis',
        'symptoms',
        'vitals',
        'objective_examination',
        'treatment_plan',
        'visit_date',
    ];

    protected function casts(): array
    {
        return [
            'vitals' => 'array',
            'visit_date' => 'datetime',
        ];
    }

    public function med(): BelongsTo
    {
        return $this->belongsTo(Med::class);
    }

    public function doctor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'doctor_id');
    }

    public function clinic(): BelongsTo
    {
        return $this->belongsTo(Clinic::class);
    }

    public function prescriptions(): HasMany
    {
        return $this->hasMany(MedPrescription::class);
    }
}
