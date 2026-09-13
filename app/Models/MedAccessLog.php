<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MedAccessLog extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $fillable = [
        'med_id',
        'user_id',
        'action',
        'details',
        'ip_address',
        'user_agent',
        'created_at',
    ];

    protected function casts(): array
    {
        return [
            'details' => 'array',
            'created_at' => 'datetime',
        ];
    }

    public function med(): BelongsTo
    {
        return $this->belongsTo(Med::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
