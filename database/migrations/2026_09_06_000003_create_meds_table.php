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
        Schema::create('meds', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('med_number', 32)->unique(); // e.g. MED-2026-7841-9012
            $table->string('card_type')->default('standard'); // standard, emergency, pediatric, chronic, senior
            $table->string('status')->default('active'); // active, locked, archived, pending_verification
            $table->string('blood_group', 10)->default('O+'); // O+, O-, A+, A-, B+, B-, AB+, AB-
            $table->string('rhesus_factor', 15)->default('positive'); // positive, negative
            $table->json('allergies')->nullable(); // list of allergies (e.g. Penicillin, NSAIDs)
            $table->json('chronic_diseases')->nullable(); // list of chronic conditions
            $table->boolean('organ_donor')->default(false);
            $table->string('emergency_contact_name')->nullable();
            $table->string('emergency_contact_phone')->nullable();
            $table->string('emergency_contact_relation')->nullable(); // ota, ona, turmush o'rtog'i
            $table->string('insurance_company')->nullable();
            $table->string('insurance_policy_number')->nullable();
            $table->date('insurance_expires_at')->nullable();
            $table->string('qr_token', 64)->unique(); // token for rapid 103 emergency scan
            $table->string('pin_code')->nullable(); // bcrypt hashed PIN (e.g. 1234)
            $table->decimal('balance_credits', 12, 2)->default(0.00); // subsidized medical allowance
            $table->date('issued_at')->nullable();
            $table->date('expires_at')->nullable();
            $table->timestamps();

            $table->index(['med_number', 'status']);
            $table->index('qr_token');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('meds');
    }
};
