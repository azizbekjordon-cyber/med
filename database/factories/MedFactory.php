<?php

namespace Database\Factories;

use App\Models\Med;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * @extends Factory<Med>
 */
class MedFactory extends Factory
{
    protected $model = Med::class;

    public function definition(): array
    {
        $year = date('Y');
        $part1 = str_pad((string) fake()->numberBetween(1000, 9999), 4, '0', STR_PAD_LEFT);
        $part2 = str_pad((string) fake()->numberBetween(1000, 9999), 4, '0', STR_PAD_LEFT);
        $medNumber = "MED-{$year}-{$part1}-{$part2}";

        return [
            'user_id' => User::factory(),
            'med_number' => $medNumber,
            'card_type' => fake()->randomElement(['standard', 'pediatric', 'chronic', 'senior']),
            'status' => 'active',
            'blood_group' => fake()->randomElement(['O+', 'O-', 'A+', 'A-', 'B+', 'B-', 'AB+', 'AB-']),
            'rhesus_factor' => fake()->randomElement(['positive', 'negative']),
            'allergies' => ['Penitsillin'],
            'chronic_diseases' => [],
            'organ_donor' => fake()->boolean(40),
            'emergency_contact_name' => fake()->name(),
            'emergency_contact_phone' => '+99890'.fake()->numerify('#######'),
            'emergency_contact_relation' => fake()->randomElement(['Otasi', 'Onasi', 'Turmush o\'rtog\'i', 'Akasi']),
            'insurance_company' => 'O\'zbekinvest Milliy Sug\'urta',
            'insurance_policy_number' => 'INS-'.fake()->numerify('######'),
            'insurance_expires_at' => now()->addYears(2),
            'qr_token' => 'MED_EMG_'.Str::upper(Str::random(24)),
            'pin_code' => Hash::make('1234'),
            'balance_credits' => 500000.00,
            'issued_at' => now()->subMonths(6),
            'expires_at' => now()->addYears(10),
        ];
    }

    public function locked(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'locked',
        ]);
    }
}
