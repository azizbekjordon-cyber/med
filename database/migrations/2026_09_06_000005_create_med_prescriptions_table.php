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
        Schema::create('med_prescriptions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('med_id')->constrained('meds')->cascadeOnDelete();
            $table->foreignId('med_record_id')->nullable()->constrained('med_records')->nullOnDelete();
            $table->foreignId('doctor_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('prescription_number', 32)->unique(); // e.g. RX-2026-88192
            $table->string('medication_name'); // e.g. Amoxicillin / Enalapril
            $table->string('dosage'); // e.g. 500 mg, 10 mg
            $table->string('frequency'); // e.g. 1 mahal ertalab, 2 mahal ovqatdan so'ng
            $table->integer('duration_days')->default(7);
            $table->text('instructions')->nullable();
            $table->string('status')->default('active'); // active, dispensed, expired, cancelled
            $table->foreignId('dispensed_pharmacy_id')->nullable()->constrained('clinics')->nullOnDelete();
            $table->dateTime('dispensed_at')->nullable();
            $table->timestamps();

            $table->index(['med_id', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('med_prescriptions');
    }
};
