<?php

use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use Inertia\Testing\AssertableInertia as Assert;

it('filters users by name or email', function () {
    $manager = User::factory()->create([
        'name' => 'Manager User',
        'email' => 'manager@example.test',
    ]);
    $manager->givePermissionTo(Permission::findOrCreate('users.view', 'web'));

    $matchingUser = User::factory()->create([
        'name' => 'Alice Example',
        'email' => 'alice@example.test',
    ]);

    User::factory()->create([
        'name' => 'Bob Example',
        'email' => 'bob@example.test',
    ]);

    $this->actingAs($manager)
        ->get(route('users.index', ['search' => 'alice']))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('users/Index')
            ->has('users.data', 1)
            ->where('users.data.0.id', $matchingUser->id)
            ->where('filters.search', 'alice'),
        );
});

it('prevents deleting the last super admin', function () {
    $manager = User::factory()->create();
    $manager->givePermissionTo(
        Permission::findOrCreate('users.view', 'web'),
        Permission::findOrCreate('users.delete', 'web'),
    );

    $superAdmin = User::factory()->create();
    $superAdmin->assignRole(Role::findOrCreate('Super Admin', 'web'));

    expect($superAdmin->isLastSuperAdmin())->toBeTrue();
    expect($manager->can('users.delete'))->toBeTrue();

    $response = $this->actingAs($manager)
        ->delete(route('users.destroy', $superAdmin))
        ->assertRedirect();

    $response->assertSessionHasErrors('message');
    $this->assertModelExists($superAdmin);
});
