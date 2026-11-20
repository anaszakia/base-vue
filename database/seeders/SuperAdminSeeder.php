<?php

namespace Database\Seeders;

use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\PermissionRegistrar;

class SuperAdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        // 1. Pastikan role Super Admin ada
        $superAdminRole = Role::findOrCreate('Super Admin');

        // 2. Berikan ALL permissions yang ada ke role Super Admin
        $allPermissions = Permission::all();
        $superAdminRole->syncPermissions($allPermissions);

        // 3. Buat atau update user Super Admin
        $superAdmin = User::updateOrCreate(
            ['email' => 'superadmin@example.com'],
            [
                'name' => 'Super Administrator',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
            ]
        );

        // 4. Assign ALL roles yang ada di database ke user Super Admin
        $allRoles = Role::all();
        $superAdmin->syncRoles($allRoles);

        // 5. Assign ALL permissions secara direct juga agar menjamin kepemilikan direct & role-based
        $superAdmin->syncPermissions($allPermissions);

        $this->command->info('Super Admin berhasil dibuat/diperbarui:');
        $this->command->info('Email    : superadmin@example.com');
        $this->command->info('Password : password');
        $this->command->info('Roles    : '.$allRoles->pluck('name')->implode(', '));
        $this->command->info('Total Permissions: '.$allPermissions->count());
    }
}
