<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Admin;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create admin user
        $adminUser = User::create([
            'name' => 'System Administrator',
            'email' => 'admin@kyle-hms.local',
            'password' => Hash::make('admin123'),
            'usertype' => 'admin',
            'email_verified_at' => now(),
        ]);

        // Create admin profile
        Admin::create([
            'user_id' => $adminUser->id,
            'phone' => '+855 12 345 678',
        ]);

        // Assign admin role
        $adminUser->assignRole('admin');

        $this->command->info('Admin user created successfully!');
        $this->command->info('Email: admin@kyle-hms.local');
        $this->command->info('Password: admin123');
    }
}
