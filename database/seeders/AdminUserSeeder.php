<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        // Super Admin
        $this->seedUser('superadmin', 'super-admin', 'SAdmin@123456', [
            'branch' => 'Head Office',
            'is_active' => true,
            'verified_at' => now(),
            'profile_completed' => true,
        ]);

        // Admin
        $this->seedUser('admin', 'admin', 'Admin@123456', [
            'branch' => 'Head Office',
            'is_active' => true,
            'verified_at' => now(),
            'profile_completed' => true,
        ]);

        // Test User
        $this->seedUser('user1_westlands', 'user', 'User@1234567', [
            'branch' => 'Westlands',
            'is_active' => true,
            'verified_at' => now(),
            'profile_completed' => false,
        ]);
    }

    /**
     * Create or update a seeded account identified by its username.
     *
     * Re-running the seeder updates the account's details so its username and
     * profile stay canonical, but never resets the password: the password is
     * only written when the account is first created. Matching includes
     * soft-deleted rows so the unique username index is never hit.
     */
    protected function seedUser(string $username, string $role, string $password, array $attributes): User
    {
        $exists = User::withTrashed()->where('username', $username)->exists();

        $user = User::withTrashed()->updateOrCreate(
            ['username' => $username],
            $exists ? $attributes : array_merge($attributes, ['password' => bcrypt($password)]),
        );

        $user->syncRoles([$role]);

        return $user;
    }
}
