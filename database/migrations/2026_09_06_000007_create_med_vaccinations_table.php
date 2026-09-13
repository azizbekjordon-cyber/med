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
        Schema::create('med_vaccinations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('med_id')->constrained('meds')->cascadeOnDelete();
            $table->string('vaccine_name'); // e.g. Gepatit B, Qizamiq-Qizilcha-Parotit (KPK)
            $table->integer('dose_number')->default(1); // 1, 2, 3, buster
            $table->string('batch_number')->nullable();
            $table->foreignId('clinic_id')->nullable()->constrained('clinics')->nullOnDelete();
            $table->foreignId('administered_by_doctor_id')->nullable()->constrained('users')->nullOnDelete();
            $table->date('administered_at');
            $table->date('next_due_date')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index(['med_id', 'administered_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('med_vaccinations');
    }
};
