<?php

namespace Database\Seeders;

use App\Models\Clinic;
use App\Models\DoctorAppointment;
use App\Models\Med;
use App\Models\MedAnalysis;
use App\Models\MedPrescription;
use App\Models\MedRecord;
use App\Models\MedReferral;
use App\Models\MedVaccination;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class MedicalSystemSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Klinika va Muassasalar
        $hospital1 = Clinic::where('license_number', 'MED-LIC-008912')->first() ?? Clinic::create([
            'name' => 'Toshkent Shahar 1-son Klinik Shifoxonasi',
            'type' => 'hospital',
            'license_number' => 'MED-LIC-008912',
            'phone' => '+998 71 244 11 22',
            'address' => 'Toshkent sh., Shayxontohur tumani, Navoiy ko\'chasi 14',
            'region' => 'Toshkent',
            'is_active' => true,
        ]);

        $emergencyCenter = Clinic::where('license_number', 'MED-LIC-103001')->first() ?? Clinic::create([
            'name' => 'Respublika Shoshilinch Tibbiy Yordam Ilmiy Markazi (103)',
            'type' => 'emergency_center',
            'license_number' => 'MED-LIC-103001',
            'phone' => '103',
            'address' => 'Toshkent sh., Chilonzor tumani, Kichik Halka Yo\'li 2',
            'region' => 'Toshkent',
            'is_active' => true,
        ]);

        $polyclinic = Clinic::where('license_number', 'MED-LIC-051022')->first() ?? Clinic::create([
            'name' => 'Yunusobod Tuman 51-son Markaziy Ko\'p Tarmoqli Poliklinika',
            'type' => 'polyclinic',
            'license_number' => 'MED-LIC-051022',
            'phone' => '+998 71 222 33 44',
            'address' => 'Toshkent sh., Yunusobod tumani, 4-mavze 18-uy',
            'region' => 'Toshkent',
            'is_active' => true,
        ]);

        $pharmacy = Clinic::where('license_number', 'PHARM-LIC-9902')->first() ?? Clinic::create([
            'name' => '"Dori-Darmon" Markaziy Davlat Dorixonasi №12',
            'type' => 'pharmacy',
            'license_number' => 'PHARM-LIC-9902',
            'phone' => '+998 71 277 88 99',
            'address' => 'Toshkent sh., Amir Temur shox ko\'chasi 45',
            'region' => 'Toshkent',
            'is_active' => true,
        ]);

        // 2. Bosh Administrator va Shifokorlar
        $admin = User::where('email', 'admin@med.uz')
            ->orWhere('pinfl', '30101800010001')
            ->first() ?? User::create([
                'name' => 'Bosh Administrator (Admin)',
                'email' => 'admin@med.uz',
                'password' => Hash::make('password123'),
                'phone' => '+998 90 000 00 00',
                'pinfl' => '30101800010001',
                'role' => 'admin',
                'specialty' => 'Tizim Bosh Boshqaruvchisi',
                'birth_date' => '1980-01-01',
                'gender' => 'male',
            ]);

        $doctor1 = User::where('email', 'yusupov@med.uz')
            ->orWhere('pinfl', '31405820010014')
            ->first() ?? User::create([
                'name' => 'Dr. Rustam Yusupov',
                'email' => 'yusupov@med.uz',
                'password' => Hash::make('password123'),
                'phone' => '+998 90 911 22 33',
                'pinfl' => '31405820010014',
                'role' => 'doctor',
                'specialty' => 'Kardiolog (Oliy toifa)',
                'doctor_license' => 'DOC-CARDIO-7721',
                'clinic_id' => $hospital1->id,
                'birth_date' => '1982-05-14',
                'gender' => 'male',
            ]);

        $doctor2 = User::where('email', 'rahimova@med.uz')
            ->orWhere('pinfl', '42207860020038')
            ->first() ?? User::create([
                'name' => 'Dr. Umida Rahimova',
                'email' => 'rahimova@med.uz',
                'password' => Hash::make('password123'),
                'phone' => '+998 91 333 44 55',
                'pinfl' => '42207860020038',
                'role' => 'doctor',
                'specialty' => 'Umumiy amaliyot shifokori (Terapevt)',
                'doctor_license' => 'DOC-THERAP-4412',
                'clinic_id' => $polyclinic->id,
                'birth_date' => '1986-07-22',
                'gender' => 'female',
            ]);

        $emergencyDoctor = User::where('email', '103@med.uz')
            ->orWhere('pinfl', '31908850010099')
            ->first() ?? User::create([
                'name' => 'Dr. Botir Ergashev',
                'email' => '103@med.uz',
                'password' => Hash::make('password123'),
                'phone' => '+998 93 555 10 30',
                'pinfl' => '31908850010099',
                'role' => 'emergency_103',
                'specialty' => 'Reanimatolog-Shoshilinch tibbiy yordam shifokori',
                'doctor_license' => 'DOC-EMERG-1034',
                'clinic_id' => $emergencyCenter->id,
                'birth_date' => '1985-08-19',
                'gender' => 'male',
            ]);

        $pharmacist = User::where('email', 'apteka@med.uz')
            ->orWhere('pinfl', '41509920030041')
            ->first() ?? User::create([
                'name' => 'Kamola Odilova',
                'email' => 'apteka@med.uz',
                'password' => Hash::make('password123'),
                'phone' => '+998 90 777 88 99',
                'pinfl' => '41509920030041',
                'role' => 'pharmacist',
                'specialty' => 'Katta provizor',
                'clinic_id' => $pharmacy->id,
                'birth_date' => '1992-09-15',
                'gender' => 'female',
            ]);

        // 3. Fuqarolar / Bemorlar va Med-Kartalar
        $userAzizbek = User::where('email', 'azizbek@med.uz')
            ->orWhere('pinfl', '32509820010025')
            ->orWhere('phone', '+998 910226667')
            ->first() ?? User::create([
                'name' => 'Azizbek Baxodirov',
                'email' => 'azizbek@med.uz',
                'password' => Hash::make('azizbek123'),
                'phone' => '+998 910226667',
                'pinfl' => '32509820010025',
                'role' => 'admin',
                'specialty' => 'Tizim Bosh Administratori',
                'birth_date' => '1995-05-14',
                'gender' => 'male',
            ]);

        $patient1 = User::where('email', 'alisher@med.uz')
            ->orWhere('pinfl', '32509820010099')
            ->first() ?? User::create([
                'name' => 'Alisher Qodirov',
                'email' => 'alisher@med.uz',
                'password' => Hash::make('password123'),
                'phone' => '+998 90 123 45 67',
                'pinfl' => '32509820010099',
                'role' => 'patient',
                'birth_date' => '1982-09-25',
                'gender' => 'male',
            ]);

        $med1 = Med::where('med_number', 'MED-2026-7841-9012')->first() ?? Med::create([
            'user_id' => $userAzizbek->id,
            'med_number' => 'MED-2026-7841-9012',
            'card_type' => 'standard',
            'status' => 'active',
            'blood_group' => 'A(II)+',
            'rhesus_factor' => 'positive',
            'allergies' => [
                'Penitsillin guruhi antibiotiklari (anafilaksiya xavfi)',
                'NSAID - Ibuprofen va Aspirin (oshqozon spazmi)',
                'Qulupnay va tsitrus mevalar',
            ],
            'chronic_diseases' => [
                'Arterial gipertoniya II bosqich, xavf 3',
                'Surunkali gastrit (remissiya davri)',
            ],
            'organ_donor' => true,
            'emergency_contact_name' => 'Nargiza Qodirova',
            'emergency_contact_phone' => '+998 90 987 65 43',
            'emergency_contact_relation' => 'Turmush o\'rtog\'i',
            'insurance_company' => 'O\'zbekinvest Milliy Sug\'urta Kompaniyasi',
            'insurance_policy_number' => 'UZ-MED-2026-98124',
            'insurance_expires_at' => '2027-12-31',
            'qr_token' => 'MED_EMG_AZIZBEK_9012_TOKEN',
            'pin_code' => Hash::make('1234'),
            'balance_credits' => 1500000.00,
            'issued_at' => '2024-01-10',
            'expires_at' => '2034-01-10',
        ]);

        // 4. Med Records (Ko'riklar va Tashxislar)
        $record1 = MedRecord::where('med_id', $med1->id)->where('icd10_code', 'I10')->first() ?? MedRecord::create([
            'med_id' => $med1->id,
            'doctor_id' => $doctor1->id,
            'clinic_id' => $hospital1->id,
            'visit_type' => 'outpatient',
            'icd10_code' => 'I10',
            'diagnosis' => 'Birlamchi (muhim) gipertoniya. II bosqich.',
            'symptoms' => 'Boshning ensa sohasida og\'riq, ko\'z oldida qorayish, tez charchash va uyqusizlik.',
            'vitals' => [
                'blood_pressure' => '145/95 mmHg',
                'heart_rate' => '84 bpm',
                'temperature' => '36.6 °C',
                'spo2' => '98%',
                'weight' => '84 kg',
                'height' => '178 cm',
                'bmi' => '26.5',
            ],
            'objective_examination' => 'Yurak chegaralari chapga 1 sm kengaygan. Tonlari ritmik, jarangdor. O\'pkada vezikulyar nafas.',
            'treatment_plan' => 'Tuz iste\'molini cheklash, doimiy qon bosimi monitoringi. Gipotenzyv dorilar rejimi belgilandi.',
            'visit_date' => now()->subDays(5),
        ]);

        $record2 = MedRecord::where('med_id', $med1->id)->where('icd10_code', 'J06.9')->first() ?? MedRecord::create([
            'med_id' => $med1->id,
            'doctor_id' => $doctor2->id,
            'clinic_id' => $polyclinic->id,
            'visit_type' => 'outpatient',
            'icd10_code' => 'J06.9',
            'diagnosis' => 'O\'tkir yuqori nafas yo\'llarining noaniq infeksiyasi (O\'RVI).',
            'symptoms' => 'Tomoqda qichishish va og\'riq, burun bitishi, tana harorati 37.8 °C gacha ko\'tarilishi.',
            'vitals' => [
                'blood_pressure' => '125/80 mmHg',
                'heart_rate' => '78 bpm',
                'temperature' => '37.6 °C',
                'spo2' => '97%',
            ],
            'objective_examination' => 'Tomoq giperemiyalangan, bodomsimon bezlar kattalashgan. O\'pkada xirillashlar yo\'q.',
            'treatment_plan' => 'Ko\'p suyuqlik ichish, tuzli eritmalar bilan tomoqni chayish, vitamin C, antipiretiklar.',
            'visit_date' => now()->subDays(20),
        ]);

        // 5. Prescriptions (Retseptlar)
        MedPrescription::where('prescription_number', 'RX-2026-99014')->first() ?? MedPrescription::create([
            'med_id' => $med1->id,
            'med_record_id' => $record1->id,
            'doctor_id' => $doctor1->id,
            'prescription_number' => 'RX-2026-99014',
            'medication_name' => 'Enalapril 10 mg (Berlin-Chemie)',
            'dosage' => '10 mg',
            'frequency' => '1 mahal ertalab och qoringa',
            'duration_days' => 30,
            'instructions' => 'Har kuni ertalab bir vaqtda qabul qilinishi shart. Qon bosimi 110/70 dan tushsa to\'xtatilsin.',
            'status' => 'active',
        ]);

        MedPrescription::where('prescription_number', 'RX-2026-44102')->first() ?? MedPrescription::create([
            'med_id' => $med1->id,
            'med_record_id' => $record1->id,
            'doctor_id' => $doctor1->id,
            'prescription_number' => 'RX-2026-44102',
            'medication_name' => 'Amlodipin 5 mg',
            'dosage' => '5 mg',
            'frequency' => '1 mahal kechqurun',
            'duration_days' => 30,
            'instructions' => 'Kechki ovqatdan 30 daqiqa keyin.',
            'status' => 'dispensed',
            'dispensed_pharmacy_id' => $pharmacy->id,
            'dispensed_at' => now()->subDays(3),
        ]);

        MedPrescription::where('prescription_number', 'RX-2026-11883')->first() ?? MedPrescription::create([
            'med_id' => $med1->id,
            'med_record_id' => $record2->id,
            'doctor_id' => $doctor2->id,
            'prescription_number' => 'RX-2026-11883',
            'medication_name' => 'Paratsetamol 500 mg',
            'dosage' => '500 mg',
            'frequency' => 'Tana harorati 38.5 °C dan oshganda',
            'duration_days' => 5,
            'instructions' => 'Kuniga maksimal 3-4 martadan oshmasin.',
            'status' => 'active',
        ]);

        // 6. Analyses (Laboratoriya va Diagnostika)
        MedAnalysis::where('med_id', $med1->id)->where('analysis_type', 'blood')->first() ?? MedAnalysis::create([
            'med_id' => $med1->id,
            'doctor_id' => $doctor1->id,
            'clinic_id' => $hospital1->id,
            'analysis_type' => 'blood',
            'title' => 'Umumiy qon tahlili (Kengaytirilgan gemogramma)',
            'indicators' => [
                ['name' => 'Gemoglobin (Hb)', 'value' => 146, 'unit' => 'g/l', 'reference' => '130 - 160', 'status' => 'normal'],
                ['name' => 'Eritrotsitlar (RBC)', 'value' => 4.7, 'unit' => 'x10^12/l', 'reference' => '4.0 - 5.1', 'status' => 'normal'],
                ['name' => 'Leykotsitlar (WBC)', 'value' => 7.4, 'unit' => 'x10^9/l', 'reference' => '4.0 - 9.0', 'status' => 'normal'],
                ['name' => 'Trombotsitlar (PLT)', 'value' => 240, 'unit' => 'x10^9/l', 'reference' => '180 - 320', 'status' => 'normal'],
                ['name' => 'SOE (ESR)', 'value' => 8, 'unit' => 'mm/soat', 'reference' => '2 - 10', 'status' => 'normal'],
            ],
            'conclusion' => 'Ko\'rsatkichlar me\'yor chegarasida. O\'tkir yallig\'lanish belgilari aniqlanmadi.',
            'status' => 'normal',
            'performed_at' => now()->subDays(6),
        ]);

        MedAnalysis::where('med_id', $med1->id)->where('analysis_type', 'biochemistry')->first() ?? MedAnalysis::create([
            'med_id' => $med1->id,
            'doctor_id' => $doctor1->id,
            'clinic_id' => $hospital1->id,
            'analysis_type' => 'biochemistry',
            'title' => 'Biokimyoviy qon tahlili va Lipid profili',
            'indicators' => [
                ['name' => 'Umumiy xolesterin', 'value' => 5.9, 'unit' => 'mmol/l', 'reference' => '3.0 - 5.2', 'status' => 'abnormal'],
                ['name' => 'Triglitseridlar', 'value' => 1.9, 'unit' => 'mmol/l', 'reference' => '0.4 - 1.7', 'status' => 'abnormal'],
                ['name' => 'Glyukoza (och qoringa)', 'value' => 5.2, 'unit' => 'mmol/l', 'reference' => '3.9 - 6.1', 'status' => 'normal'],
                ['name' => 'Kreatinin', 'value' => 84, 'unit' => 'mkmol/l', 'reference' => '62 - 115', 'status' => 'normal'],
                ['name' => 'ALT', 'value' => 28, 'unit' => 'U/l', 'reference' => '< 41', 'status' => 'normal'],
            ],
            'conclusion' => 'O\'rtacha dislipidemiya (xolesterin ko\'rsatkichi me\'yordan yuqori). Gipoxolesterin parhez tavsiya etiladi.',
            'status' => 'abnormal',
            'performed_at' => now()->subDays(6),
        ]);

        MedAnalysis::where('med_id', $med1->id)->where('analysis_type', 'ecg')->first() ?? MedAnalysis::create([
            'med_id' => $med1->id,
            'doctor_id' => $doctor1->id,
            'clinic_id' => $hospital1->id,
            'analysis_type' => 'ecg',
            'title' => 'Elektrokardiogramma (12 tarmoqli EKG)',
            'indicators' => [
                ['name' => 'Yurak urish tezligi', 'value' => 82, 'unit' => 'urish/daq', 'reference' => '60 - 90', 'status' => 'normal'],
                ['name' => 'PQ intervali', 'value' => 0.16, 'unit' => 'sek', 'reference' => '0.12 - 0.20', 'status' => 'normal'],
                ['name' => 'QRS kompleksi', 'value' => 0.09, 'unit' => 'sek', 'reference' => '0.06 - 0.10', 'status' => 'normal'],
            ],
            'conclusion' => 'Sinusli ritm. Elektr o\'qi chapga og\'gan. Chap qorincha miokardining mo\'tadil gipertrofiyasi belgilari.',
            'status' => 'normal',
            'performed_at' => now()->subDays(5),
        ]);

        // 7. Vaccinations (Emlashlar)
        MedVaccination::where('med_id', $med1->id)->where('batch_number', 'HEP-B-2023-889')->first() ?? MedVaccination::create([
            'med_id' => $med1->id,
            'vaccine_name' => 'Gepatit B ga qarshi vaksina (Engerix-B)',
            'dose_number' => 3,
            'batch_number' => 'HEP-B-2023-889',
            'clinic_id' => $polyclinic->id,
            'administered_by_doctor_id' => $doctor2->id,
            'administered_at' => '2023-04-12',
            'notes' => 'To\'liq kurs yakunlangan. Asoratsiz o\'tgan.',
        ]);

        MedVaccination::where('med_id', $med1->id)->where('batch_number', 'FLU-2025-091')->first() ?? MedVaccination::create([
            'med_id' => $med1->id,
            'vaccine_name' => 'Mavsumiy grippga qarshi (Grippol Plus)',
            'dose_number' => 1,
            'batch_number' => 'FLU-2025-091',
            'clinic_id' => $polyclinic->id,
            'administered_by_doctor_id' => $doctor2->id,
            'administered_at' => '2025-10-15',
            'next_due_date' => '2026-10-15',
            'notes' => 'Yillik qayta emlash tavsiya etiladi.',
        ]);

        // 8. Referrals (Yo'llanmalar)
        MedReferral::where('med_id', $med1->id)->where('specialty_needed', 'Kardiolog')->first() ?? MedReferral::create([
            'med_id' => $med1->id,
            'referring_doctor_id' => $doctor2->id,
            'referring_clinic_id' => $polyclinic->id,
            'target_clinic_id' => $hospital1->id,
            'specialty_needed' => 'Kardiolog',
            'reason' => 'Arterial bosimning beqaror ko\'tarilishi sababli kardiolog maslahati va EKG tekshiruvi.',
            'urgency' => 'routine',
            'status' => 'completed',
            'expires_at' => now()->addDays(30),
        ]);

        MedReferral::where('med_id', $med1->id)->where('specialty_needed', 'Nevrolog')->first() ?? MedReferral::create([
            'med_id' => $med1->id,
            'referring_doctor_id' => $doctor1->id,
            'referring_clinic_id' => $hospital1->id,
            'target_clinic_id' => $hospital1->id,
            'specialty_needed' => 'Nevrolog',
            'reason' => 'Bosh og\'riqlari va qon aylanishi buzilishi bo\'yicha maslahat.',
            'urgency' => 'urgent',
            'status' => 'pending',
            'expires_at' => now()->addDays(14),
        ]);

        // 9. Doctor Appointments (E-Navbat va Talonlar)
        DoctorAppointment::where('ticket_number', 'NAV-'.date('Y').'-001')->first() ?? DoctorAppointment::create([
            'med_id' => $med1->id,
            'patient_id' => $patient1->id,
            'doctor_id' => $doctor1->id,
            'clinic_id' => $hospital1->id,
            'appointment_date' => now()->format('Y-m-d'),
            'appointment_time' => '10:00',
            'queue_number' => 1,
            'ticket_number' => 'NAV-'.date('Y').'-001',
            'room_number' => ($doctor1->id * 102).' - xona',
            'reason' => 'Gipotenziv dori vositalari dozasini sozlash va EKG nazorati',
            'status' => 'pending',
        ]);

        DoctorAppointment::where('ticket_number', 'NAV-'.date('Y').'-003')->first() ?? DoctorAppointment::create([
            'med_id' => $med1->id,
            'patient_id' => $patient1->id,
            'doctor_id' => $doctor2->id,
            'clinic_id' => $polyclinic->id,
            'appointment_date' => now()->addDays(2)->format('Y-m-d'),
            'appointment_time' => '11:30',
            'queue_number' => 3,
            'ticket_number' => 'NAV-'.date('Y').'-003',
            'room_number' => ($doctor2->id * 102).' - xona',
            'reason' => 'Profilaktik umumiy ko\'rik',
            'status' => 'pending',
        ]);

        // 10. Access Logs (Audit jurnali)
        if ($med1->accessLogs()->count() === 0) {
            $med1->logAccess($patient1->id, 'patient_view', ['source' => 'web_portal', 'device' => 'Desktop Chrome']);
            $med1->logAccess($doctor1->id, 'doctor_full_view', ['specialty' => 'Kardiolog', 'verification' => 'pin_verified']);
            $med1->logAccess($emergencyDoctor->id, 'emergency_qr_view', ['brigade' => '103-Toshkent-12', 'access_mode' => 'rapid_triage']);
            $med1->logAccess($pharmacist->id, 'prescription_dispense', ['prescription_number' => 'RX-2026-44102', 'pharmacy' => 'Dori-Darmon №12']);
        }
    }
}
