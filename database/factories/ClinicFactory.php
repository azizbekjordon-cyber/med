<?php

namespace Database\Factories;

use App\Models\Clinic;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Clinic>
 */
class ClinicFactory extends Factory
{
    protected $model = Clinic::class;

    public function definition(): array
    {
        return [
            'name' => fake()->company().' Klinikasi',
            'type' => fake()->randomElement(['hospital', 'polyclinic', 'pharmacy', 'diagnostic_center', 'emergency_center']),
            'license_number' => 'LIC-'.fake()->numerify('######'),
            'phone' => '+998 71 '.fake()->numerify('### ## ##'),
            'address' => fake()->address(),
            'region' => 'Toshkent',
            'is_active' => true,
        ];
    }

    public function pharmacy(): static
    {
        return $this->state(fn (array $attributes) => [
            'type' => 'pharmacy',
            'name' => 'Dorixona №'.fake()->numerify('##'),
        ]);
    }

    public function hospital(): static
    {
        return $this->state(fn (array $attributes) => [
            'type' => 'hospital',
        ]);
    }
}
