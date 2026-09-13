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
        Schema::table('users', function (Blueprint $table) {
            $table->string('phone')->nullable()->after('email');
            $table->string('pinfl', 14)->nullable()->unique()->after('phone'); // JSHSHIR (14-digit citizen ID)
            $table->string('role')->default('patient')->after('pinfl'); // patient, doctor, emergency_103, pharmacist, admin
            $table->string('specialty')->nullable()->after('role'); // e.g. Kardiolog, Terapevt
            $table->string('doctor_license')->nullable()->after('specialty');
            $table->foreignId('clinic_id')->nullable()->constrained('clinics')->nullOnDelete()->after('doctor_license');
            $table->date('birth_date')->nullable()->after('clinic_id');
            $table->string('gender')->nullable()->after('birth_date'); // male, female
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['clinic_id']);
            $table->dropColumn([
                'phone',
                'pinfl',
                'role',
                'specialty',
                'doctor_license',
                'clinic_id',
                'birth_date',
                'gender',
            ]);
        });
    }
};
