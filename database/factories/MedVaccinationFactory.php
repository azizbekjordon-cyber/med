<?php

namespace Database\Factories;

use App\Models\Clinic;
use App\Models\Med;
use App\Models\MedVaccination;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<MedVaccination>
 */
class MedVaccinationFactory extends Factory
{
    protected $model = MedVaccination::class;

    public function definition(): array
    {
        return [
            'med_id' => Med::factory(),
            'vaccine_name' => 'Gepatit B ga qarshi vaksina',
            'dose_number' => 1,
            'batch_number' => 'VAC-'.fake()->numerify('#####'),
            'clinic_id' => Clinic::factory(),
            'administered_by_doctor_id' => User::factory()->doctor(),
            'administered_at' => now(),
            'next_due_date' => now()->addMonths(6),
            'notes' => 'Asoratsiz o\'tgan.',
        ];
    }
}
