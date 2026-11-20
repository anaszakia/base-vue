<?php

namespace Database\Seeders;

use App\Models\Menu;
use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;
use Spatie\Permission\PermissionRegistrar;

class RolePermissionAndMenuSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        // 1. Create Permissions
        $permissions = [
            'dashboard.view',
            // Access & Roles management
            'roles.view',
            'roles.create',
            'roles.edit',
            'roles.delete',
            // Permissions management
            'permissions.view',
            'permissions.create',
            'permissions.edit',
            'permissions.delete',
            // Menus management
            'menus.view',
            'menus.create',
            'menus.edit',
            'menus.delete',
            // Users management
            'users.view',
            'users.create',
            'users.edit',
            'users.delete',
        ];

        foreach ($permissions as $permission) {
            Permission::findOrCreate($permission);
        }

        // 2. Create Roles
        $superAdminRole = Role::findOrCreate('Super Admin');
        $superAdminRole->syncPermissions(Permission::all());

        $adminRole = Role::findOrCreate('Admin');
        $adminRole->syncPermissions([
            'dashboard.view',
            'users.view',
            'roles.view',
            'menus.view',
        ]);

        $userRole = Role::findOrCreate('User');
        $userRole->syncPermissions([
            'dashboard.view',
        ]);

        // 3. Create Default Menus
        Menu::query()->delete();

        // Dashboard
        Menu::create([
            'title' => 'Dashboard',
            'route_or_url' => 'dashboard',
            'icon' => 'LayoutGrid',
            'permission_name' => 'dashboard.view',
            'order' => 1,
            'is_active' => true,
        ]);

        // Master Akses & Pengaturan (Parent)
        $systemMenu = Menu::create([
            'title' => 'Sistem & Akses',
            'route_or_url' => null,
            'icon' => 'Shield',
            'permission_name' => 'roles.view',
            'order' => 2,
            'is_active' => true,
        ]);

        // Child: Roles
        Menu::create([
            'parent_id' => $systemMenu->id,
            'title' => 'Role & Hak Akses',
            'route_or_url' => 'roles.index',
            'icon' => 'ShieldCheck',
            'permission_name' => 'roles.view',
            'order' => 1,
            'is_active' => true,
        ]);

        // Child: Permissions
        Menu::create([
            'parent_id' => $systemMenu->id,
            'title' => 'Permissions',
            'route_or_url' => 'permissions.index',
            'icon' => 'Key',
            'permission_name' => 'permissions.view',
            'order' => 2,
            'is_active' => true,
        ]);

        // Child: Setting Menus
        Menu::create([
            'parent_id' => $systemMenu->id,
            'title' => 'Manajemen Menu',
            'route_or_url' => 'menus.index',
            'icon' => 'Menu',
            'permission_name' => 'menus.view',
            'order' => 3,
            'is_active' => true,
        ]);

        // Assign Super Admin role to the first user if exists
        $firstUser = User::first();
        if ($firstUser) {
            $firstUser->assignRole($superAdminRole);
        }
    }
}
