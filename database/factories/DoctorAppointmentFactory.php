<?php

namespace Database\Factories;

use App\Models\Clinic;
use App\Models\DoctorAppointment;
use App\Models\Med;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<DoctorAppointment>
 */
class DoctorAppointmentFactory extends Factory
{
    protected $model = DoctorAppointment::class;

    public function definition(): array
    {
        $queueNumber = fake()->numberBetween(1, 20);
        $ticketCode = 'NAV-'.date('Y').'-'.str_pad((string) $queueNumber, 3, '0', STR_PAD_LEFT);

        return [
            'med_id' => Med::factory(),
            'patient_id' => User::factory(),
            'doctor_id' => User::factory()->doctor(),
            'clinic_id' => Clinic::factory(),
            'appointment_date' => now()->format('Y-m-d'),
            'appointment_time' => '10:00',
            'queue_number' => $queueNumber,
            'ticket_number' => $ticketCode,
            'room_number' => '102-xona',
            'reason' => 'Profilaktik tibbiy ko\'rik',
            'status' => 'pending',
        ];
    }

    public function completed(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'completed',
        ]);
    }

    public function cancelled(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'cancelled',
        ]);
    }
}
