<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Doctor;
use App\Models\Specialty;
use Illuminate\Support\Facades\Hash;

class DoctorSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $doctors = [
            [
                'name' => 'Sok Lina',
                'email' => 'lina.sok@kyle-hms.local',
                'specialty' => 'Cardiology',
                'phone' => '+855 12 111 001',
                'license' => 'MD-CAR-2015-001',
                'qualifications' => 'MD, Fellowship in Cardiology',
                'experience' => 8,
                'bio' => 'Specialized in cardiovascular diseases with focus on preventive cardiology.',
            ],
            [
                'name' => 'Chan Veasna',
                'email' => 'veasna.chan@kyle-hms.local',
                'specialty' => 'Pediatrics',
                'phone' => '+855 12 111 002',
                'license' => 'MD-PED-2016-002',
                'qualifications' => 'MD, Pediatrics Specialist',
                'experience' => 7,
                'bio' => 'Dedicated to providing comprehensive care for children and adolescents.',
            ],
            [
                'name' => 'Meng Sophea',
                'email' => 'sophea.meng@kyle-hms.local',
                'specialty' => 'General Surgery',
                'phone' => '+855 12 111 003',
                'license' => 'MD-SUR-2014-003',
                'qualifications' => 'MD, FRCS, General Surgery',
                'experience' => 10,
                'bio' => 'Experienced surgeon specializing in minimally invasive procedures.',
            ],
            [
                'name' => 'Ly Bopha',
                'email' => 'bopha.ly@kyle-hms.local',
                'specialty' => 'Internal Medicine',
                'phone' => '+855 12 111 004',
                'license' => 'MD-INT-2017-004',
                'qualifications' => 'MD, Internal Medicine',
                'experience' => 6,
                'bio' => 'Expert in diagnosing and treating complex adult diseases.',
            ],
            [
                'name' => 'Chea Dara',
                'email' => 'dara.chea@kyle-hms.local',
                'specialty' => 'Obstetrics & Gynecology',
                'phone' => '+855 12 111 005',
                'license' => 'MD-OBG-2015-005',
                'qualifications' => 'MD, OB/GYN Specialist',
                'experience' => 8,
                'bio' => 'Committed to women\'s health throughout all stages of life.',
            ],
            [
                'name' => 'Pich Ratana',
                'email' => 'ratana.pich@kyle-hms.local',
                'specialty' => 'Orthopedics',
                'phone' => '+855 12 111 006',
                'license' => 'MD-ORT-2016-006',
                'qualifications' => 'MD, Orthopedic Surgery',
                'experience' => 7,
                'bio' => 'Specialized in joint replacement and sports injuries.',
            ],
            [
                'name' => 'Heng Sothea',
                'email' => 'sothea.heng@kyle-hms.local',
                'specialty' => 'Dermatology',
                'phone' => '+855 12 111 007',
                'license' => 'MD-DER-2018-007',
                'qualifications' => 'MD, Dermatology Specialist',
                'experience' => 5,
                'bio' => 'Expert in medical and cosmetic dermatology procedures.',
            ],
            [
                'name' => 'Kem Chanthy',
                'email' => 'chanthy.kem@kyle-hms.local',
                'specialty' => 'Neurology',
                'phone' => '+855 12 111 008',
                'license' => 'MD-NEU-2014-008',
                'qualifications' => 'MD, Neurology, PhD',
                'experience' => 10,
                'bio' => 'Focuses on neurological disorders and brain health.',
            ],
            [
                'name' => 'Nuth Sreypov',
                'email' => 'sreypov.nuth@kyle-hms.local',
                'specialty' => 'Ophthalmology',
                'phone' => '+855 12 111 009',
                'license' => 'MD-OPH-2017-009',
                'qualifications' => 'MD, Eye Surgery Specialist',
                'experience' => 6,
                'bio' => 'Specialized in cataract surgery and vision correction.',
            ],
            [
                'name' => 'Sam Kunthea',
                'email' => 'kunthea.sam@kyle-hms.local',
                'specialty' => 'Psychiatry',
                'phone' => '+855 12 111 010',
                'license' => 'MD-PSY-2016-010',
                'qualifications' => 'MD, Psychiatry',
                'experience' => 7,
                'bio' => 'Compassionate care for mental health and emotional well-being.',
            ],
            [
                'name' => 'Keo Virak',
                'email' => 'virak.keo@kyle-hms.local',
                'specialty' => 'Emergency Medicine',
                'phone' => '+855 12 111 011',
                'license' => 'MD-EMR-2018-011',
                'qualifications' => 'MD, Emergency Medicine',
                'experience' => 5,
                'bio' => 'Experienced in acute care and emergency procedures.',
            ],
            [
                'name' => 'Thy Piseth',
                'email' => 'piseth.thy@kyle-hms.local',
                'specialty' => 'Radiology',
                'phone' => '+855 12 111 012',
                'license' => 'MD-RAD-2015-012',
                'qualifications' => 'MD, Diagnostic Radiology',
                'experience' => 8,
                'bio' => 'Expert in medical imaging and diagnostic procedures.',
            ],
        ];

        foreach ($doctors as $doctorData) {
            // Create user
            $user = User::create([
                'name' => $doctorData['name'],
                'email' => $doctorData['email'],
                'password' => Hash::make('doctor123'),
                'usertype' => 'doctor',
                'email_verified_at' => now(),
            ]);

            // Get specialty
            $specialty = Specialty::where('name', $doctorData['specialty'])->first();

            // Create doctor profile
            $doctor = Doctor::create([
                'user_id' => $user->id,
                'specialty_id' => $specialty->id,
                'phone' => $doctorData['phone'],
                'license_number' => $doctorData['license'],
                'qualifications' => $doctorData['qualifications'],
                'years_of_experience' => $doctorData['experience'],
                'bio' => $doctorData['bio'],
                'is_available' => true,
            ]);

            // Assign doctor role
            $user->assignRole('doctor');

            $this->command->info("Doctor created: {$doctorData['name']} ({$doctorData['specialty']})");
        }

        $this->command->info('All doctors seeded successfully!');
        $this->command->info('Default password for all doctors: doctor123');
    }
}
