<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'phone',
        'pinfl',
        'role',
        'specialty',
        'doctor_license',
        'clinic_id',
        'birth_date',
        'gender',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'birth_date' => 'date',
        ];
    }

    public function meds(): HasMany
    {
        return $this->hasMany(Med::class);
    }

    public function primaryMed(): HasOne
    {
        return $this->hasOne(Med::class)->where('status', 'active')->latestOfMany();
    }

    public function clinic(): BelongsTo
    {
        return $this->belongsTo(Clinic::class);
    }

    public function doctorRecords(): HasMany
    {
        return $this->hasMany(MedRecord::class, 'doctor_id');
    }

    public function doctorPrescriptions(): HasMany
    {
        return $this->hasMany(MedPrescription::class, 'doctor_id');
    }

    public function isDoctor(): bool
    {
        return $this->role === 'doctor';
    }

    public function isEmergency103(): bool
    {
        return $this->role === 'emergency_103';
    }

    public function isPharmacist(): bool
    {
        return $this->role === 'pharmacist';
    }

    public function isPatient(): bool
    {
        return $this->role === 'patient';
    }

    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }
}
