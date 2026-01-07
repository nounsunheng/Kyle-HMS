<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->command->info('🌱 Starting database seeding...');
        $this->command->newLine();

        $this->call([
            RolePermissionSeeder::class,
            SpecialtySeeder::class,
            AdminSeeder::class,
            DoctorSeeder::class,
            PatientSeeder::class,
        ]);

        $this->command->newLine();
        $this->command->info('✅ Database seeding completed successfully!');
        $this->command->newLine();

        $this->command->info('📝 Login Credentials:');
        $this->command->info('═══════════════════════════════════════════');
        $this->command->info('👨‍💼 Admin:');
        $this->command->info('   Email: admin@kyle-hms.local');
        $this->command->info('   Password: admin123');
        $this->command->newLine();
        $this->command->info('👨‍⚕️  Doctors:');
        $this->command->info('   Email: [firstname].[lastname]@kyle-hms.local');
        $this->command->info('   Example: lina.sok@kyle-hms.local');
        $this->command->info('   Password: doctor123');
        $this->command->newLine();
        $this->command->info('👥 Patients:');
        $this->command->info('   Email: sunheng@example.com (or any patient email)');
        $this->command->info('   Password: patient123');
        $this->command->info('═══════════════════════════════════════════');
    }
}
