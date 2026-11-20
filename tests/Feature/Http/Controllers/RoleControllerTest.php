<?php

use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use Inertia\Testing\AssertableInertia as Assert;

it('paginates roles when viewing the second page', function () {
    $user = User::factory()->create();
    $user->givePermissionTo(Permission::findOrCreate('roles.view', 'web'));

    foreach (range(1, 11) as $number) {
        Role::create([
            'name' => 'Pagination role '.$number,
            'guard_name' => 'web',
        ]);
    }

    $this->actingAs($user)
        ->get(route('roles.index', ['page' => 2]))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('roles/Index')
            ->has('roles.data', 1)
            ->where('roles.current_page', 2)
            ->where('roles.total', 11),
        );
});

it('redirects to the last role page when the requested page is out of range', function () {
    $user = User::factory()->create();
    $user->givePermissionTo(Permission::findOrCreate('roles.view', 'web'));

    foreach (range(1, 11) as $number) {
        Role::create([
            'name' => 'Pagination role '.$number,
            'guard_name' => 'web',
        ]);
    }

    $this->actingAs($user)
        ->get(route('roles.index', ['page' => 99]))
        ->assertRedirect(route('roles.index', ['page' => 2]));
});
