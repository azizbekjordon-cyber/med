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
        Schema::create('doctor_appointments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('med_id')->constrained('meds')->cascadeOnDelete();
            $table->foreignId('patient_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('doctor_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('clinic_id')->constrained('clinics')->cascadeOnDelete();
            $table->date('appointment_date');
            $table->string('appointment_time'); // e.g., '09:30', '14:00'
            $table->unsignedInteger('queue_number')->default(1); // 1, 2, 3, 4
            $table->string('ticket_number')->unique(); // e.g. NAV-2026-004
            $table->string('room_number')->nullable(); // e.g. 204-xona
            $table->string('reason')->nullable(); // Bemor shikoyati
            $table->string('status')->default('pending'); // pending, in_consultation, completed, cancelled
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index(['doctor_id', 'appointment_date']);
            $table->index(['patient_id', 'appointment_date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('doctor_appointments');
    }
};
