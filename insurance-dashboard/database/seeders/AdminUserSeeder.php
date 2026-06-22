<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::create([
            'first_name' => 'Admin',
            'last_name' => 'User',
            'name' => 'Admin User',
            'email' => 'admin@insurance.com',
            'password' => bcrypt('Admin@123456'),
            'mobile' => '+254700000000',
            'gender' => 'male',
            'is_active' => true,
            'profile_completed' => true,
        ]);
        $admin->assignRole('super-admin');

        $user = User::create([
            'first_name' => 'Test',
            'last_name' => 'User',
            'name' => 'Test User',
            'email' => 'user@insurance.com',
            'password' => bcrypt('User@123456'),
            'mobile' => '+254711000000',
            'gender' => 'female',
            'is_active' => true,
            'profile_completed' => false,
        ]);
        $user->assignRole('user');
    }
}
