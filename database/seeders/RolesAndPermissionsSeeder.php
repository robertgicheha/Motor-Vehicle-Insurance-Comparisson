<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolesAndPermissionsSeeder extends Seeder
{
    public function run(): void
    {
        app()->make(\Spatie\Permission\PermissionRegistrar::class)->forgetCachedPermissions();

        $permissions = [
            'view users', 'create users', 'edit users', 'delete users', 'activate users', 'deactivate users',
            'view roles', 'create roles', 'edit roles', 'delete roles', 'assign roles',
            'view permissions', 'create permissions', 'edit permissions', 'delete permissions',
            'view quotations', 'create quotations', 'edit quotations', 'delete quotations', 'approve quotations',
            'view companies', 'create companies', 'edit companies', 'delete companies', 'activate companies',
            'view rates', 'create rates', 'edit rates', 'delete rates',
            'view reports', 'generate reports', 'export reports',
            'view audit logs', 'view activity logs',
            'view settings', 'edit settings',
            'view dashboard', 'access admin panel',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission, 'guard_name' => 'web']);
        }

        $superAdmin = Role::firstOrCreate(['name' => 'super-admin', 'guard_name' => 'web']);
        $superAdmin->givePermissionTo(Permission::all());

        $admin = Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);
        $admin->givePermissionTo([
            'view users', 'create users', 'edit users', 'activate users', 'deactivate users',
            'view quotations', 'create quotations', 'edit quotations', 'delete quotations',
            'view companies', 'create companies', 'edit companies', 'activate companies',
            'view rates', 'create rates', 'edit rates', 'delete rates',
            'view reports', 'generate reports', 'export reports',
            'view audit logs', 'view activity logs',
            'view settings', 'edit settings',
            'view dashboard', 'access admin panel',
        ]);

        $user = Role::firstOrCreate(['name' => 'user', 'guard_name' => 'web']);
        $user->givePermissionTo([
            'view quotations', 'create quotations',
            'view companies', 'view rates',
            'view dashboard',
        ]);
    }
}
