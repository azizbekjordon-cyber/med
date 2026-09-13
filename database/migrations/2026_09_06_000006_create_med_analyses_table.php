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
        Schema::create('med_analyses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('med_id')->constrained('meds')->cascadeOnDelete();
            $table->foreignId('doctor_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('clinic_id')->nullable()->constrained('clinics')->nullOnDelete();
            $table->string('analysis_type')->default('blood'); // blood, biochemistry, urine, mri, ct, ecg, ultrasound
            $table->string('title'); // e.g. Umumiy qon tahlili, Biokimyoviy tahlil
            $table->json('indicators')->nullable(); // structured test parameters & normal ranges
            $table->text('conclusion')->nullable();
            $table->string('status')->default('normal'); // normal, abnormal, critical, pending
            $table->string('attachment_url')->nullable();
            $table->dateTime('performed_at');
            $table->timestamps();

            $table->index(['med_id', 'performed_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('med_analyses');
    }
};
