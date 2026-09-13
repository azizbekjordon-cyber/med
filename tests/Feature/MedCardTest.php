<?php

namespace Tests\Feature;

use App\Models\Clinic;
use App\Models\DoctorAppointment;
use App\Models\Med;
use App\Models\MedPrescription;
use App\Models\User;
use Database\Seeders\MedicalSystemSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class MedCardTest extends TestCase
{
    use RefreshDatabase;

    public function test_med_portal_homepage_loads_successfully(): void
    {
        $this->seed(MedicalSystemSeeder::class);

        $response = $this->get('/');

        $response->assertStatus(200);
        $response->assertSee('MED');
        $response->assertSee('Tibbiy Karta');
    }

    public function test_med_portal_loads_successfully_without_prior_seeding(): void
    {
        $this->assertEquals(0, Med::count());

        $user = User::factory()->create(['role' => 'admin']);
        $this->actingAs($user);

        $response = $this->get('/med');

        $response->assertStatus(200);
        $response->assertSee('MED');
        $this->assertGreaterThan(0, Med::count());
    }

    public function test_emergency_103_triage_api_returns_critical_patient_data(): void
    {
        $user = User::factory()->create(['name' => 'Rustam Aliyev']);
        $med = Med::factory()->create([
            'user_id' => $user->id,
            'med_number' => 'MED-2026-9999-0001',
            'card_type' => 'emergency',
            'status' => 'active',
            'blood_group' => 'B(III)+',
            'rhesus_factor' => 'positive',
            'allergies' => ['Penitsillin'],
            'chronic_diseases' => ['Qandli diabet 2-tur'],
            'organ_donor' => true,
            'emergency_contact_name' => 'Otabek Aliyev',
            'emergency_contact_phone' => '+998901234567',
            'qr_token' => 'MED_EMG_TEST_TOKEN_123',
            'balance_credits' => 0,
        ]);

        $response = $this->getJson('/api/v1/emergency/triage/MED_EMG_TEST_TOKEN_123');

        $response->assertStatus(200);
        $response->assertJson([
            'status' => 'success',
            'mode' => 'EMERGENCY_TRIAGE_ACCESS',
            'data' => [
                'med_number' => 'MED-2026-9999-0001',
                'blood_group' => 'B(III)+',
                'allergies' => ['Penitsillin'],
            ],
        ]);

        // Assert audit log was recorded
        $this->assertDatabaseHas('med_access_logs', [
            'med_id' => $med->id,
            'action' => 'emergency_qr_view',
        ]);
    }

    public function test_med_card_pin_verification(): void
    {
        $user = User::factory()->create();
        $med = Med::factory()->create([
            'user_id' => $user->id,
            'med_number' => 'MED-2026-8888-0002',
            'card_type' => 'standard',
            'status' => 'active',
            'blood_group' => 'A(II)+',
            'rhesus_factor' => 'positive',
            'pin_code' => Hash::make('4321'),
            'qr_token' => 'MED_EMG_TEST_PIN_123',
            'balance_credits' => 0,
        ]);

        // Correct PIN
        $correctResponse = $this->postJson("/api/v1/meds/{$med->med_number}/verify-pin", [
            'pin' => '4321',
        ]);
        $correctResponse->assertStatus(200);
        $correctResponse->assertJson(['verified' => true]);

        // Incorrect PIN
        $wrongResponse = $this->postJson("/api/v1/meds/{$med->med_number}/verify-pin", [
            'pin' => '0000',
        ]);
        $wrongResponse->assertStatus(403);
    }

    public function test_prescription_can_be_dispensed(): void
    {
        $pharmacy = Clinic::factory()->pharmacy()->create([
            'name' => 'Sinov Dorixonasi',
        ]);

        $med = Med::factory()->create([
            'med_number' => 'MED-2026-7777-0003',
            'qr_token' => 'MED_EMG_TEST_RX_123',
        ]);

        $prescription = MedPrescription::factory()->create([
            'med_id' => $med->id,
            'prescription_number' => 'RX-TEST-999',
            'medication_name' => 'Ketanov 10mg',
            'status' => 'active',
        ]);

        $response = $this->postJson("/api/v1/prescriptions/{$prescription->prescription_number}/dispense", [
            'pharmacy_clinic_id' => $pharmacy->id,
        ]);

        $response->assertStatus(200);
        $this->assertDatabaseHas('med_prescriptions', [
            'prescription_number' => 'RX-TEST-999',
            'status' => 'dispensed',
            'dispensed_pharmacy_id' => $pharmacy->id,
        ]);
    }

    public function test_card_status_can_be_toggled(): void
    {
        $med = Med::factory()->create([
            'status' => 'active',
        ]);

        // Toggle to locked
        $response = $this->post("/med/{$med->med_number}/status");
        $response->assertRedirect();
        $this->assertEquals('locked', $med->fresh()->status);

        // Toggle back to active
        $response = $this->post("/med/{$med->med_number}/status");
        $response->assertRedirect();
        $this->assertEquals('active', $med->fresh()->status);
    }

    public function test_web_add_analysis(): void
    {
        $med = Med::factory()->create();
        $doctor = User::factory()->doctor()->create();
        $clinic = Clinic::factory()->create();

        $response = $this->post("/med/{$med->med_number}/analyses", [
            'doctor_id' => $doctor->id,
            'clinic_id' => $clinic->id,
            'analysis_type' => 'blood',
            'title' => 'Umumiy qon tahlili testi',
            'indicators_raw' => "Gemoglobin: 145 g/l\nLeykotsitlar: 7.2 x10^9/l",
            'conclusion' => 'Me\'yorda',
            'status' => 'normal',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('med_analyses', [
            'med_id' => $med->id,
            'title' => 'Umumiy qon tahlili testi',
            'analysis_type' => 'blood',
        ]);
    }

    public function test_web_add_vaccination(): void
    {
        $med = Med::factory()->create();
        $doctor = User::factory()->doctor()->create();
        $clinic = Clinic::factory()->create();

        $response = $this->post("/med/{$med->med_number}/vaccinations", [
            'administered_by_doctor_id' => $doctor->id,
            'clinic_id' => $clinic->id,
            'vaccine_name' => 'COVID-19 Sputnik V',
            'dose_number' => 1,
            'batch_number' => 'VAC-TEST-777',
            'administered_at' => now()->format('Y-m-d'),
            'notes' => 'Asoratsiz',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('med_vaccinations', [
            'med_id' => $med->id,
            'vaccine_name' => 'COVID-19 Sputnik V',
            'batch_number' => 'VAC-TEST-777',
        ]);
    }

    public function test_web_add_referral(): void
    {
        $med = Med::factory()->create();
        $doctor = User::factory()->doctor()->create();
        $clinic1 = Clinic::factory()->create();
        $clinic2 = Clinic::factory()->create();

        $response = $this->post("/med/{$med->med_number}/referrals", [
            'referring_doctor_id' => $doctor->id,
            'referring_clinic_id' => $clinic1->id,
            'target_clinic_id' => $clinic2->id,
            'specialty_needed' => 'Nevropatolog',
            'reason' => 'Bosh og\'riqlarini tekshirish',
            'urgency' => 'routine',
            'expires_days' => 30,
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('med_referrals', [
            'med_id' => $med->id,
            'specialty_needed' => 'Nevropatolog',
            'target_clinic_id' => $clinic2->id,
        ]);
    }

    public function test_web_book_and_complete_appointment(): void
    {
        $med = Med::factory()->create();
        $doctor = User::factory()->doctor()->create();
        $clinic = Clinic::factory()->create();

        // 1. Check doctor slots
        $slotsResponse = $this->getJson("/med/appointments/slots?doctor_id={$doctor->id}&date=".now()->format('Y-m-d'));
        $slotsResponse->assertStatus(200);
        $slotsResponse->assertJson(['status' => 'success']);

        // 2. Book appointment
        $bookResponse = $this->post('/med/appointments/book', [
            'med_id' => $med->id,
            'doctor_id' => $doctor->id,
            'appointment_date' => now()->format('Y-m-d'),
            'appointment_time' => '10:00',
            'reason' => 'Test konsultatsiya',
        ]);
        $bookResponse->assertRedirect();

        $appointment = DoctorAppointment::where('med_id', $med->id)->first();
        $this->assertNotNull($appointment);
        $this->assertEquals('pending', $appointment->status);

        // 3. Complete appointment
        $completeResponse = $this->post("/med/appointments/{$appointment->id}/complete");
        $completeResponse->assertRedirect();
        $this->assertEquals('completed', $appointment->fresh()->status);
    }

    public function test_api_vaccinations_endpoints(): void
    {
        $med = Med::factory()->create();
        $doctor = User::factory()->doctor()->create();
        $clinic = Clinic::factory()->create();

        // Store vaccination via API
        $storeResponse = $this->postJson("/api/v1/meds/{$med->med_number}/vaccinations", [
            'vaccine_name' => 'Engerix-B',
            'dose_number' => 2,
            'batch_number' => 'HEP-002',
            'clinic_id' => $clinic->id,
            'administered_by_doctor_id' => $doctor->id,
        ]);
        $storeResponse->assertStatus(201);
        $storeResponse->assertJson(['status' => 'success']);

        // Index vaccinations via API
        $indexResponse = $this->getJson("/api/v1/meds/{$med->med_number}/vaccinations");
        $indexResponse->assertStatus(200);
        $indexResponse->assertJsonPath('data.data.0.vaccine_name', 'Engerix-B');
    }

    public function test_api_referrals_endpoints(): void
    {
        $med = Med::factory()->create();
        $doctor = User::factory()->doctor()->create();
        $clinic = Clinic::factory()->create();

        // Store referral via API
        $storeResponse = $this->postJson("/api/v1/meds/{$med->med_number}/referrals", [
            'target_clinic_id' => $clinic->id,
            'referring_doctor_id' => $doctor->id,
            'specialty_needed' => 'Oftalmolog',
            'reason' => 'Ko\'z bosimini o\'lchash',
            'urgency' => 'routine',
        ]);
        $storeResponse->assertStatus(201);
        $storeResponse->assertJson(['status' => 'success']);

        // Index referrals via API
        $indexResponse = $this->getJson("/api/v1/meds/{$med->med_number}/referrals");
        $indexResponse->assertStatus(200);
        $indexResponse->assertJsonPath('data.data.0.specialty_needed', 'Oftalmolog');
    }

    public function test_scan_page_loads_with_all_tabs(): void
    {
        $this->seed(MedicalSystemSeeder::class);
        $med = Med::first();

        $response = $this->get("/scan/{$med->qr_token}");

        $response->assertStatus(200);
        $response->assertSee('MED-PASS VERIFIED');
        $response->assertSee('Tahlillar');
        $response->assertSee("Emlash & Yo'llanma", false);
    }

    public function test_portal_page_has_one_click_dispense(): void
    {
        $this->seed(MedicalSystemSeeder::class);
        $med = Med::first();

        $response = $this->get('/?med='.$med->med_number);

        $response->assertStatus(200);
        $response->assertSee('Dori berish (1-klik)');
        $response->assertSee('dispenseRx(', false);
    }
}
