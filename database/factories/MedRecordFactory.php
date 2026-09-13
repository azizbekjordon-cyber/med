<?php

namespace Database\Factories;

use App\Models\Clinic;
use App\Models\Med;
use App\Models\MedRecord;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<MedRecord>
 */
class MedRecordFactory extends Factory
{
    protected $model = MedRecord::class;

    public function definition(): array
    {
        return [
            'med_id' => Med::factory(),
            'doctor_id' => User::factory()->doctor(),
            'clinic_id' => Clinic::factory(),
            'visit_type' => 'outpatient',
            'icd10_code' => 'I10',
            'diagnosis' => 'Birlamchi gipertoniya. II bosqich.',
            'symptoms' => 'Bosh og\'rig\'i va charchoq.',
            'vitals' => [
                'blood_pressure' => '130/85 mmHg',
                'heart_rate' => '78 bpm',
                'temperature' => '36.6 °C',
                'spo2' => '98%',
            ],
            'objective_examination' => 'Umumiy holati qoniqarli.',
            'treatment_plan' => 'Gipotenziv dorilar rejimi.',
            'visit_date' => now(),
        ];
    }
}
