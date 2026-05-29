<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class SuperAdminSeeder extends Seeder
{
    public function run(): void
    {
        $superAdmin = User::create([
            'name' => 'Super Admin',
            'email' => 'admin@btguru.in',
            'phone' => '+91-9999999999',
            'password' => Hash::make('SuperAdmin@123'),
            'status' => 'active',
            'tenant_id' => null,
            'email_verified_at' => now(),
        ]);

        $superAdmin->assignRole('super_admin');

        $this->command->info('Super Admin created successfully!');
        $this->command->info('Email: admin@btguru.in');
        $this->command->info('Password: SuperAdmin@123');
    }
}
