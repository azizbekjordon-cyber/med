<?php

namespace Database\Factories;

use App\Models\Clinic;
use App\Models\Med;
use App\Models\MedAnalysis;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<MedAnalysis>
 */
class MedAnalysisFactory extends Factory
{
    protected $model = MedAnalysis::class;

    public function definition(): array
    {
        return [
            'med_id' => Med::factory(),
            'doctor_id' => User::factory()->doctor(),
            'clinic_id' => Clinic::factory(),
            'analysis_type' => 'blood',
            'title' => 'Umumiy qon tahlili',
            'indicators' => [
                ['name' => 'Gemoglobin', 'value' => 140, 'unit' => 'g/l', 'reference' => '130 - 160', 'status' => 'normal'],
                ['name' => 'Leykotsitlar', 'value' => 6.5, 'unit' => 'x10^9/l', 'reference' => '4.0 - 9.0', 'status' => 'normal'],
            ],
            'conclusion' => 'Ko\'rsatkichlar me\'yorda.',
            'status' => 'normal',
            'performed_at' => now(),
        ];
    }
}
