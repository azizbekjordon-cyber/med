<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * @extends Factory<User>
 */
class UserFactory extends Factory
{
    /**
     * The current password being used by the factory.
     */
    protected static ?string $password;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->name(),
            'email' => fake()->unique()->safeEmail(),
            'email_verified_at' => now(),
            'password' => static::$password ??= Hash::make('password'),
            'remember_token' => Str::random(10),
            'phone' => '+99890'.fake()->numerify('#######'),
            'pinfl' => fake()->unique()->numerify('##############'),
            'role' => 'patient',
            'birth_date' => fake()->dateTimeBetween('-60 years', '-18 years')->format('Y-m-d'),
            'gender' => fake()->randomElement(['male', 'female']),
        ];
    }

    /**
     * Indicate that the model's email address should be unverified.
     */
    public function unverified(): static
    {
        return $this->state(fn (array $attributes) => [
            'email_verified_at' => null,
        ]);
    }

    public function doctor(?int $clinicId = null, string $specialty = 'Kardiolog'): static
    {
        return $this->state(fn (array $attributes) => [
            'role' => 'doctor',
            'clinic_id' => $clinicId,
            'specialty' => $specialty,
            'doctor_license' => 'DOC-'.fake()->numerify('#####'),
        ]);
    }

    public function emergency103(?int $clinicId = null): static
    {
        return $this->state(fn (array $attributes) => [
            'role' => 'emergency_103',
            'clinic_id' => $clinicId,
            'specialty' => 'Reanimatolog-Shoshilinch tibbiy yordam shifokori',
            'doctor_license' => 'DOC-EMERG-'.fake()->numerify('####'),
        ]);
    }

    public function pharmacist(?int $clinicId = null): static
    {
        return $this->state(fn (array $attributes) => [
            'role' => 'pharmacist',
            'clinic_id' => $clinicId,
            'specialty' => 'Provizor',
        ]);
    }

    public function patient(): static
    {
        return $this->state(fn (array $attributes) => [
            'role' => 'patient',
        ]);
    }
}
