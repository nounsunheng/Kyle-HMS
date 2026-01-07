<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Specialty;

class SpecialtySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $specialties = [
            ['name' => 'Cardiology', 'description' => 'Heart and cardiovascular system'],
            ['name' => 'Dermatology', 'description' => 'Skin, hair, and nails'],
            ['name' => 'Endocrinology', 'description' => 'Hormones and metabolism'],
            ['name' => 'Gastroenterology', 'description' => 'Digestive system'],
            ['name' => 'General Surgery', 'description' => 'Surgical procedures'],
            ['name' => 'Hematology', 'description' => 'Blood disorders'],
            ['name' => 'Internal Medicine', 'description' => 'Adult diseases'],
            ['name' => 'Nephrology', 'description' => 'Kidney diseases'],
            ['name' => 'Neurology', 'description' => 'Nervous system'],
            ['name' => 'Obstetrics & Gynecology', 'description' => 'Women\'s health'],
            ['name' => 'Oncology', 'description' => 'Cancer treatment'],
            ['name' => 'Ophthalmology', 'description' => 'Eye care'],
            ['name' => 'Orthopedics', 'description' => 'Bones and joints'],
            ['name' => 'Otolaryngology', 'description' => 'Ear, nose, and throat'],
            ['name' => 'Pediatrics', 'description' => 'Children\'s health'],
            ['name' => 'Psychiatry', 'description' => 'Mental health'],
            ['name' => 'Pulmonology', 'description' => 'Respiratory system'],
            ['name' => 'Radiology', 'description' => 'Medical imaging'],
            ['name' => 'Rheumatology', 'description' => 'Joint and autoimmune diseases'],
            ['name' => 'Urology', 'description' => 'Urinary system'],
            ['name' => 'Anesthesiology', 'description' => 'Anesthesia and pain management'],
            ['name' => 'Emergency Medicine', 'description' => 'Acute care'],
            ['name' => 'Family Medicine', 'description' => 'Primary care for all ages'],
            ['name' => 'Geriatrics', 'description' => 'Elderly care'],
            ['name' => 'Infectious Disease', 'description' => 'Infectious diseases'],
            ['name' => 'Pathology', 'description' => 'Disease diagnosis'],
            ['name' => 'Physical Medicine', 'description' => 'Rehabilitation'],
            ['name' => 'Plastic Surgery', 'description' => 'Reconstructive surgery'],
            ['name' => 'Sports Medicine', 'description' => 'Athletic injuries'],
            ['name' => 'Allergy & Immunology', 'description' => 'Allergies and immune system'],
        ];

        foreach ($specialties as $specialty) {
            Specialty::create($specialty);
        }

        $this->command->info('30 medical specialties seeded successfully!');
    }
}
