<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('med_records', function (Blueprint $table) {
            $table->id();
            $table->foreignId('med_id')->constrained('meds')->cascadeOnDelete();
            $table->foreignId('doctor_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('clinic_id')->nullable()->constrained('clinics')->nullOnDelete();
            $table->string('visit_type')->default('outpatient'); // outpatient, inpatient, emergency, telemed
            $table->string('icd10_code')->nullable(); // e.g. I10 (Essential hypertension), J06.9
            $table->string('diagnosis'); // Clinical diagnosis text
            $table->text('symptoms')->nullable(); // Patient complaints
            $table->json('vitals')->nullable(); // BP (120/80), Heart rate (75), Temp (36.6), SpO2 (98%)
            $table->text('objective_examination')->nullable(); // Physical examination notes
            $table->text('treatment_plan')->nullable(); // Prescribed therapy & regimen
            $table->dateTime('visit_date');
            $table->timestamps();

            $table->index(['med_id', 'visit_date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('med_records');
    }
};
