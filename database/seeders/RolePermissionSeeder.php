<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolePermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Create permissions
        $permissions = [
            // Patient permissions
            'view-doctors',
            'book-appointment',
            'view-own-appointments',
            'cancel-own-appointment',
            'view-own-medical-records',
            'update-own-profile',

            // Doctor permissions
            'view-assigned-patients',
            'create-schedule',
            'update-schedule',
            'delete-schedule',
            'view-appointments',
            'update-appointment-status',
            'create-medical-record',
            'view-medical-records',
            'update-own-doctor-profile',

            // Admin permissions
            'manage-users',
            'manage-doctors',
            'manage-patients',
            'manage-specialties',
            'view-all-appointments',
            'cancel-any-appointment',
            'view-all-schedules',
            'view-system-reports',
            'manage-roles-permissions',
        ];

        foreach ($permissions as $permission) {
            Permission::create(['name' => $permission]);
        }

        // Create roles and assign permissions

        // Patient role
        $patientRole = Role::create(['name' => 'patient']);
        $patientRole->givePermissionTo([
            'view-doctors',
            'book-appointment',
            'view-own-appointments',
            'cancel-own-appointment',
            'view-own-medical-records',
            'update-own-profile',
        ]);

        // Doctor role
        $doctorRole = Role::create(['name' => 'doctor']);
        $doctorRole->givePermissionTo([
            'view-assigned-patients',
            'create-schedule',
            'update-schedule',
            'delete-schedule',
            'view-appointments',
            'update-appointment-status',
            'create-medical-record',
            'view-medical-records',
            'update-own-doctor-profile',
        ]);

        // Admin role
        $adminRole = Role::create(['name' => 'admin']);
        $adminRole->givePermissionTo(Permission::all());

        $this->command->info('Roles and permissions created successfully!');
    }
}
