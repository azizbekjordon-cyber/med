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
        Schema::create('med_referrals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('med_id')->constrained('meds')->cascadeOnDelete();
            $table->foreignId('referring_doctor_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('referring_clinic_id')->nullable()->constrained('clinics')->nullOnDelete();
            $table->foreignId('target_clinic_id')->nullable()->constrained('clinics')->nullOnDelete();
            $table->string('specialty_needed'); // e.g. Neyroxirurg, Onkolog
            $table->text('reason'); // Referral motive
            $table->string('urgency')->default('routine'); // routine, urgent, emergency
            $table->string('status')->default('pending'); // pending, accepted, completed, rejected
            $table->date('expires_at')->nullable();
            $table->timestamps();

            $table->index(['med_id', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('med_referrals');
    }
};
