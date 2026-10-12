<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\PermissionRegistrar;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        // 1. Permission: "boleh melakukan APA".
        $permissions = [
            'users.view',
            'users.create',
            'users.update',
            'users.delete',
            'users.assign-role',
        ];

        foreach ($permissions as $name) {
            Permission::firstOrCreate(['name' => $name, 'guard_name' => 'web']);
        }

        // 2. Role: "SIAPA" + tempeli permission-nya.
        $adminRole = Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);
        $adminRole->syncPermissions($permissions);

        $userRole = Role::firstOrCreate(['name' => 'user', 'guard_name' => 'web']);
        $userRole->syncPermissions(['users.view', 'users.update']);

        // 3. Akun demo + tempel role-nya.
        $admin = User::updateOrCreate(
            ['email' => 'admin@example.com'],
            [
                'name' => 'Admin Demo',
                'password' => 'password',
            ]
        );
        $admin->syncRoles(['admin']);

        $user = User::updateOrCreate(
            ['email' => 'user@example.com'],
            [
                'name' => 'User Demo',
                'password' => 'password',
            ]
        );
        $user->syncRoles(['user']);
    }
}
