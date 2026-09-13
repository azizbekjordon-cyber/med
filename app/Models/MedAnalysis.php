<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MedAnalysis extends Model
{
    use HasFactory;

    protected $fillable = [
        'med_id',
        'doctor_id',
        'clinic_id',
        'analysis_type',
        'title',
        'indicators',
        'conclusion',
        'status',
        'attachment_url',
        'performed_at',
    ];

    protected function casts(): array
    {
        return [
            'indicators' => 'array',
            'performed_at' => 'datetime',
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
}
