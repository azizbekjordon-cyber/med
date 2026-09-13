<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MedVaccination extends Model
{
    use HasFactory;

    protected $fillable = [
        'med_id',
        'vaccine_name',
        'dose_number',
        'batch_number',
        'clinic_id',
        'administered_by_doctor_id',
        'administered_at',
        'next_due_date',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'administered_at' => 'date',
            'next_due_date' => 'date',
            'dose_number' => 'integer',
        ];
    }

    public function med(): BelongsTo
    {
        return $this->belongsTo(Med::class);
    }

    public function clinic(): BelongsTo
    {
        return $this->belongsTo(Clinic::class);
    }

    public function doctor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'administered_by_doctor_id');
    }
}
