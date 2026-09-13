<?php

namespace Database\Factories;

use App\Models\Clinic;
use App\Models\Med;
use App\Models\MedPrescription;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<MedPrescription>
 */
class MedPrescriptionFactory extends Factory
{
    protected $model = MedPrescription::class;

    public function definition(): array
    {
        $code = str_pad((string) fake()->numberBetween(10000, 99999), 5, '0', STR_PAD_LEFT);

        return [
            'med_id' => Med::factory(),
            'doctor_id' => User::factory()->doctor(),
            'prescription_number' => 'RX-'.date('Y')."-{$code}",
            'medication_name' => 'Enalapril 10 mg',
            'dosage' => '10 mg',
            'frequency' => '1 mahal ertalab',
            'duration_days' => 14,
            'instructions' => 'Ovqatdan so\'ng ko\'p suv bilan ichilsin.',
            'status' => 'active',
        ];
    }

    public function dispensed(?int $pharmacyId = null): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'dispensed',
            'dispensed_pharmacy_id' => $pharmacyId ?? Clinic::factory()->pharmacy(),
            'dispensed_at' => now(),
        ]);
    }
}
