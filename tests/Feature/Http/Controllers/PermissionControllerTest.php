<?php

use App\Models\Permission;
use App\Models\User;
use Inertia\Testing\AssertableInertia as Assert;

it('paginates permissions when viewing the second page', function () {
    $user = User::factory()->create();
    $user->givePermissionTo(Permission::findOrCreate('permissions.view', 'web'));

    foreach (range(1, 25) as $number) {
        Permission::create([
            'name' => 'pagination-test-'.$number,
            'guard_name' => 'web',
        ]);
    }

    $this->actingAs($user)
        ->get(route('permissions.index', ['page' => 2]))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('permissions/Index')
            ->has('permissions.data', 10)
            ->where('permissions.current_page', 2),
        );
});

it('redirects to the last permission page when the requested page is out of range', function () {
    $user = User::factory()->create();
    $user->givePermissionTo(Permission::findOrCreate('permissions.view', 'web'));

    foreach (range(1, 25) as $number) {
        Permission::create([
            'name' => 'pagination-test-'.$number,
            'guard_name' => 'web',
        ]);
    }

    $response = $this->actingAs($user)
        ->get(route('permissions.index', ['page' => 99]));

    $lastPage = (int) ceil(Permission::count() / 10);

    $response->assertRedirect(route('permissions.index', ['page' => $lastPage]));
});
