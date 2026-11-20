<?php

use App\Models\Menu;
use App\Models\Permission;
use App\Models\User;
use Inertia\Testing\AssertableInertia as Assert;

it('paginates menus when viewing the second page', function () {
    $user = User::factory()->create();
    $user->givePermissionTo(Permission::findOrCreate('menus.view', 'web'));

    foreach (range(1, 11) as $order) {
        Menu::create([
            'title' => 'Menu '.$order,
            'order' => $order,
        ]);
    }

    $this->actingAs($user)
        ->get(route('menus.index', ['page' => 2]))
        ->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('menus/Index')
            ->has('menus.data', 1)
            ->where('menus.current_page', 2)
            ->where('menus.total', 11),
        );
});

it('redirects to the last menu page when the requested page is out of range', function () {
    $user = User::factory()->create();
    $user->givePermissionTo(Permission::findOrCreate('menus.view', 'web'));

    foreach (range(1, 11) as $order) {
        Menu::create([
            'title' => 'Menu '.$order,
            'order' => $order,
        ]);
    }

    $this->actingAs($user)
        ->get(route('menus.index', ['page' => 99]))
        ->assertRedirect(route('menus.index', ['page' => 2]));
});
