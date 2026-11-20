<?php

use App\Models\Menu;
use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use Inertia\Testing\AssertableInertia as Assert;

it('renders dashboard statistics and summaries from models', function () {
    $user = User::factory()->create();
    Role::create(['name' => 'Dashboard Role', 'guard_name' => 'web']);
    Permission::create(['name' => 'dashboard.test', 'guard_name' => 'web']);
    Menu::create(['title' => 'Dashboard Menu', 'order' => 1]);

    $this->actingAs($user)
        ->get(route('dashboard'))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('Dashboard')
            ->where('stats.total_users', 1)
            ->where('stats.total_roles', 1)
            ->where('stats.total_permissions', 1)
            ->where('stats.total_menus', 1)
            ->has('recentUsers', 1)
            ->has('rolesSummary', 1)
            ->has('recentActivities'),
        );
});
