<?php

namespace Database\Factories;

use App\Models\Clinic;
use App\Models\Med;
use App\Models\MedReferral;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<MedReferral>
 */
class MedReferralFactory extends Factory
{
    protected $model = MedReferral::class;

    public function definition(): array
    {
        return [
            'med_id' => Med::factory(),
            'referring_doctor_id' => User::factory()->doctor(),
            'referring_clinic_id' => Clinic::factory(),
            'target_clinic_id' => Clinic::factory(),
            'specialty_needed' => 'Kardiolog',
            'reason' => 'Konsultatsiya va instrumental tekshiruv uchun yo\'llanma.',
            'urgency' => 'routine',
            'status' => 'pending',
            'expires_at' => now()->addDays(30),
        ];
    }
}
