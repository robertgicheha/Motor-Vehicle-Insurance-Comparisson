<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        // Super Admin
        $superAdmin = User::firstOrCreate(
            ['email' => 'superadmin@insurance.com'],
            [
                'first_name' => 'Super',
                'last_name' => 'Admin',
                'name' => 'Super Admin',
                'password' => bcrypt('SAdmin@123456'),
                'mobile' => '+254710351517',
                'gender' => 'male',
                'is_active' => true,
                'profile_completed' => true,
            ]
        );

        $superAdmin->assignRole('super-admin');

        // Admin
        $admin = User::firstOrCreate(
            ['email' => 'admin@insurance.com'],
            [
                'first_name' => 'Admin',
                'last_name' => 'User',
                'name' => 'Admin User',
                'password' => bcrypt('Admin@123456'),
                'mobile' => '+254710351517',
                'gender' => 'male',
                'is_active' => true,
                'profile_completed' => true,
            ]
        );

        $admin->assignRole('admin');

        // Test User
        $user = User::firstOrCreate(
            ['email' => 'user@insurance.com'],
            [
                'first_name' => 'Test',
                'last_name' => 'User',
                'name' => 'Test User',
                'password' => bcrypt('User@123456'),
                'mobile' => '+254711000000',
                'gender' => 'female',
                'is_active' => true,
                'profile_completed' => false,
            ]
        );

        $user->assignRole('user');
    }
}