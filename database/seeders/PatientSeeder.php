<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Patient;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class PatientSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $patients = [
            [
                'name' => 'Noun Sunheng',
                'email' => 'sunheng@example.com',
                'phone' => '+855 96 999 0399',
                'dob' => '2003-05-29',
                'gender' => 'male',
                'address' => 'Phnom Penh, Cambodia',
                'emergency_contact' => '+855 12 345 678',
                'blood_type' => 'O+',
                'allergies' => 'None',
            ],
            [
                'name' => 'Chea Sreymom',
                'email' => 'sreymom@example.com',
                'phone' => '+855 12 222 001',
                'dob' => '1995-03-15',
                'gender' => 'female',
                'address' => 'Phnom Penh, Cambodia',
                'emergency_contact' => '+855 12 222 002',
                'blood_type' => 'A+',
                'allergies' => 'Penicillin',
            ],
            [
                'name' => 'Lim Sokha',
                'email' => 'sokha@example.com',
                'phone' => '+855 12 333 001',
                'dob' => '1988-07-20',
                'gender' => 'male',
                'address' => 'Siem Reap, Cambodia',
                'emergency_contact' => '+855 12 333 002',
                'blood_type' => 'B+',
                'allergies' => 'None',
            ],
            [
                'name' => 'Vong Samnang',
                'email' => 'samnang@example.com',
                'phone' => '+855 12 444 001',
                'dob' => '2000-11-10',
                'gender' => 'male',
                'address' => 'Battambang, Cambodia',
                'emergency_contact' => '+855 12 444 002',
                'blood_type' => 'AB+',
                'allergies' => 'Dust, Pollen',
            ],
            [
                'name' => 'Phal Kannitha',
                'email' => 'kannitha@example.com',
                'phone' => '+855 12 555 001',
                'dob' => '1992-01-25',
                'gender' => 'female',
                'address' => 'Phnom Penh, Cambodia',
                'emergency_contact' => '+855 12 555 002',
                'blood_type' => 'O-',
                'allergies' => 'Shellfish',
            ],
            [
                'name' => 'Kong Darith',
                'email' => 'darith@example.com',
                'phone' => '+855 12 666 001',
                'dob' => '1985-09-30',
                'gender' => 'male',
                'address' => 'Kampong Cham, Cambodia',
                'emergency_contact' => '+855 12 666 002',
                'blood_type' => 'A-',
                'allergies' => 'None',
            ],
            [
                'name' => 'Heng Chanty',
                'email' => 'chanty@example.com',
                'phone' => '+855 12 777 001',
                'dob' => '1998-04-12',
                'gender' => 'female',
                'address' => 'Phnom Penh, Cambodia',
                'emergency_contact' => '+855 12 777 002',
                'blood_type' => 'B-',
                'allergies' => 'Latex',
            ],
            [
                'name' => 'Mao Rithy',
                'email' => 'rithy@example.com',
                'phone' => '+855 12 888 001',
                'dob' => '1990-06-18',
                'gender' => 'male',
                'address' => 'Sihanoukville, Cambodia',
                'emergency_contact' => '+855 12 888 002',
                'blood_type' => 'AB-',
                'allergies' => 'None',
            ],
            [
                'name' => 'Sok Pheakdey',
                'email' => 'pheakdey@example.com',
                'phone' => '+855 12 999 001',
                'dob' => '2001-12-05',
                'gender' => 'male',
                'address' => 'Phnom Penh, Cambodia',
                'emergency_contact' => '+855 12 999 002',
                'blood_type' => 'O+',
                'allergies' => 'Bee stings',
            ],
            [
                'name' => 'Khim Sopheak',
                'email' => 'sopheak@example.com',
                'phone' => '+855 11 111 001',
                'dob' => '1987-08-22',
                'gender' => 'male',
                'address' => 'Kandal, Cambodia',
                'emergency_contact' => '+855 11 111 002',
                'blood_type' => 'A+',
                'allergies' => 'None',
            ],
        ];

        foreach ($patients as $patientData) {
            // Create user
            $user = User::create([
                'name' => $patientData['name'],
                'email' => $patientData['email'],
                'password' => Hash::make('patient123'),
                'usertype' => 'patient',
                'email_verified_at' => now(),
            ]);

            // Create patient profile
            $patient = Patient::create([
                'user_id' => $user->id,
                'phone' => $patientData['phone'],
                'date_of_birth' => Carbon::parse($patientData['dob']),
                'gender' => $patientData['gender'],
                'address' => $patientData['address'],
                'emergency_contact' => $patientData['emergency_contact'],
                'blood_type' => $patientData['blood_type'],
                'allergies' => $patientData['allergies'],
            ]);

            // Assign patient role
            $user->assignRole('patient');

            $this->command->info("Patient created: {$patientData['name']}");
        }

        $this->command->info('All patients seeded successfully!');
        $this->command->info('Default password for all patients: patient123');
    }
}
