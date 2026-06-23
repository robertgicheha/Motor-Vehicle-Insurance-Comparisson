<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::firstOrCreate(
            ['email' => 'admin@insurance.com'],
            [
                'first_name' => 'Admin',
                'last_name' => 'User',
                'name' => 'Admin User',
                'password' => bcrypt('Admin@123456'),
                'mobile' => '+254700000000',
                'gender' => 'male',
                'is_active' => true,
                'profile_completed' => true,
            ]
        );
        $admin->assignRole('super-admin');

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
