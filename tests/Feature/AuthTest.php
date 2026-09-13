<?php

namespace Tests\Feature;

use App\Models\User;
use Database\Seeders\MedicalSystemSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Auth;
use Tests\TestCase;

class AuthTest extends TestCase
{
    use RefreshDatabase;

    public function test_login_page_renders_successfully(): void
    {
        $response = $this->get('/login');

        $response->assertStatus(200);
        $response->assertSee('MED');
        $response->assertSee('Tizimga Kirish');
        $response->assertSee('Ism va Familiyangiz');
        $response->assertSee('Telefon raqam yoki Gmail');
        $response->assertSee('id="nameInput"', false);
        $response->assertSee('name="name"', false);
    }

    public function test_azizbek_baxodirov_logs_in_as_admin_with_phone_and_password(): void
    {
        $response = $this->post('/login', [
            'name' => 'Azizbek Baxodirov',
            'login' => '+998 910226667',
            'password' => 'azizbek123',
            'role' => 'admin',
        ]);

        $response->assertRedirect(route('med.index'));
        $this->assertTrue(Auth::check());
        $this->assertEquals('Azizbek Baxodirov', Auth::user()->name);
        $this->assertTrue(Auth::user()->isAdmin());
    }

    public function test_admin_retains_admin_role_even_if_login_role_parameter_was_patient(): void
    {
        $response = $this->post('/login', [
            'login' => '+998 910226667',
            'password' => 'azizbek123',
            'role' => 'patient',
        ]);

        $response->assertRedirect(route('med.index'));
        $this->assertTrue(Auth::check());
        $this->assertTrue(Auth::user()->isAdmin());
        $this->assertEquals('admin', Auth::user()->role);
    }

    public function test_admin_can_login_with_email_and_password(): void
    {
        $this->seed(MedicalSystemSeeder::class);

        $response = $this->post('/login', [
            'name' => 'Bosh Administrator',
            'login' => 'admin@med.uz',
            'password' => 'password123',
            'role' => 'admin',
        ]);

        $response->assertRedirect(route('med.index'));
        $this->assertTrue(Auth::check());
        $this->assertTrue(Auth::user()->isAdmin());
    }

    public function test_patient_can_login_with_phone_and_password(): void
    {
        $this->seed(MedicalSystemSeeder::class);

        $response = $this->post('/login', [
            'name' => 'Alisher Qodirov',
            'login' => '+998 90 123 45 67',
            'password' => 'password123',
            'role' => 'patient',
        ]);

        $response->assertRedirect(route('med.index'));
        $this->assertTrue(Auth::check());
        $this->assertEquals('Alisher Qodirov', Auth::user()->name);
    }

    public function test_new_user_is_registered_and_logged_in_automatically(): void
    {
        $response = $this->post('/login', [
            'name' => 'Dilshod Rahmatov',
            'login' => '+998 90 999 88 77',
            'password' => 'securepass123',
            'role' => 'admin',
        ]);

        $response->assertRedirect(route('med.index'));
        $this->assertTrue(Auth::check());
        $this->assertEquals('Dilshod Rahmatov', Auth::user()->name);
        $this->assertDatabaseHas('users', [
            'name' => 'Dilshod Rahmatov',
            'phone' => '+998 90 999 88 77',
            'role' => 'admin',
        ]);
    }

    public function test_login_fails_with_invalid_password(): void
    {
        $this->seed(MedicalSystemSeeder::class);

        $response = $this->post('/login', [
            'login' => 'admin@med.uz',
            'password' => 'wrongpassword',
            'role' => 'admin',
        ]);

        $response->assertSessionHasErrors('password');
        $this->assertFalse(Auth::check());
    }

    public function test_ajax_login_returns_json_response(): void
    {
        $this->seed(MedicalSystemSeeder::class);

        $response = $this->postJson('/login', [
            'login' => 'yusupov@med.uz',
            'password' => 'password123',
            'role' => 'doctor',
        ]);

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
            'redirect' => route('med.index'),
        ]);
        $this->assertTrue(Auth::check());
        $this->assertEquals('yusupov@med.uz', Auth::user()->email);
    }

    public function test_user_can_logout(): void
    {
        $this->seed(MedicalSystemSeeder::class);
        $user = User::where('email', 'admin@med.uz')->first();

        $response = $this->actingAs($user)->post('/logout');

        $response->assertRedirect(route('login'));
        $this->assertFalse(Auth::check());
    }

    public function test_logged_in_user_visiting_login_redirects_to_med(): void
    {
        $this->seed(MedicalSystemSeeder::class);
        $user = User::where('email', 'admin@med.uz')->first();

        $response = $this->actingAs($user)->get('/login');

        $response->assertRedirect(route('med.index'));
    }

    public function test_registered_user_name_is_reflected_on_med_card_and_site(): void
    {
        $this->seed(MedicalSystemSeeder::class);

        $response = $this->post('/login', [
            'name' => 'Azizbek Baxodirov',
            'login' => '+998 910226667',
            'password' => 'azizbek123',
            'role' => 'admin',
        ]);

        $response->assertRedirect(route('med.index'));

        $portalResponse = $this->get('/med');
        $portalResponse->assertStatus(200);
        $portalResponse->assertSee('AZIZBEK BAXODIROV');
        $portalResponse->assertSee('Azizbek Baxodirov');
    }

    public function test_another_new_user_name_is_reflected_on_med_card_and_site(): void
    {
        $this->seed(MedicalSystemSeeder::class);

        $response = $this->post('/login', [
            'name' => 'Rustam Fayziyev',
            'login' => '+998 90 777 55 44',
            'password' => 'secret1234',
            'role' => 'doctor',
        ]);

        $response->assertRedirect(route('med.index'));
        $user = Auth::user();
        $this->assertNotNull($user);
        $this->assertEquals('Rustam Fayziyev', $user->name);

        $portalResponse = $this->actingAs($user)->get('/med');
        $portalResponse->assertStatus(200);
        $portalResponse->assertSee('RUSTAM FAYZIYEV');
        $portalResponse->assertSee('Rustam Fayziyev');
    }

    public function test_patient_login_hides_clinical_actions_like_add_record_and_prescription(): void
    {
        $this->seed(MedicalSystemSeeder::class);

        // Bemor sifatida kirish
        $response = $this->post('/login', [
            'name' => 'Sardor Rahimiy',
            'login' => '+998 90 123 99 88',
            'password' => 'pass1234',
            'role' => 'patient',
        ]);

        $response->assertRedirect(route('med.index'));
        $user = Auth::user();
        $this->assertNotNull($user);
        $this->assertTrue($user->isPatient());

        $portalResponse = $this->actingAs($user)->get('/med');
        $portalResponse->assertStatus(200);

        // Bemor uchun dori qo'shish va qon bosimi / ko'rik kiritish joylari ko'rinmasligi kerak
        $portalResponse->assertDontSee("Ko'rik Qo'shish", false);
        $portalResponse->assertDontSee('Ko&#039;rik Qo&#039;shish');
        $portalResponse->assertDontSee('Retsept Yozish');
        $portalResponse->assertDontSee('Yangi Med-Karta');
        $portalResponse->assertDontSee("Yangi Tahlil Qo'shish");
        $portalResponse->assertDontSee('addRecordModal');
        $portalResponse->assertDontSee('addPrescriptionModal');

        // Lekin bemor uchun navbat olish va o'z ma'lumotlari mavjud bo'lishi kerak
        $portalResponse->assertSee('Navbat Olish');
        $portalResponse->assertSee('Sardor Rahimiy');
    }

    public function test_doctor_or_admin_can_see_clinical_actions(): void
    {
        $this->seed(MedicalSystemSeeder::class);

        // Shifokor yoki admin sifatida kirish
        $response = $this->post('/login', [
            'name' => 'Dr. Bobur Karimov',
            'login' => '+998 90 999 11 22',
            'password' => 'doctorpass123',
            'role' => 'doctor',
        ]);

        $response->assertRedirect(route('med.index'));
        $user = Auth::user();
        $this->assertTrue($user->isDoctor());

        $portalResponse = $this->actingAs($user)->get('/med');
        $portalResponse->assertStatus(200);

        // Shifokor / admin uchun ko'rik va retsept kiritish joylari mavjud bo'lishi kerak
        $portalResponse->assertSee("Ko'rik Qo'shish", false);
        $portalResponse->assertSee('Retsept Yozish');
        $portalResponse->assertSee('Yangi Med-Karta');
        $portalResponse->assertSee('addRecordModal');
        $portalResponse->assertSee('addPrescriptionModal');
    }

    public function test_login_fails_without_selecting_role(): void
    {
        $response = $this->post('/login', [
            'name' => 'Azizbek',
            'login' => 'azizbek.jordon@gmail.com',
            'password' => 'password123',
        ]);

        $response->assertSessionHasErrors('role');
        $this->assertFalse(Auth::check());
    }

    public function test_ajax_login_fails_without_selecting_role(): void
    {
        $response = $this->postJson('/login', [
            'name' => 'Azizbek',
            'login' => 'azizbek.jordon@gmail.com',
            'password' => 'password123',
        ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors('role');
        $this->assertFalse(Auth::check());
    }
}
