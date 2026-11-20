<?php

use Laravel\Fortify\Features;

beforeEach(function () {
    $this->skipUnlessFortifyHas(Features::registration());
});

test('registration screen can be rendered', function () {
    $response = $this->get(route('register'));

    $response->assertOk();
});

test('new users can register', function () {
    $response = $this->post(route('register.store'), [
        'name' => 'Test User',
        'email' => 'test@example.com',
        'password' => 'password',
        'password_confirmation' => 'password',
    ]);

    $this->assertAuthenticated();
    $response->assertRedirect(route('dashboard', absolute: false));
});

test('registration is limited to three submissions per IP for fifteen minutes', function () {
    $invalidRegistration = [];
    $firstIp = '198.51.100.10';
    $secondIp = '198.51.100.11';

    for ($attempt = 0; $attempt < 3; $attempt++) {
        $this->withServerVariables(['REMOTE_ADDR' => $firstIp])
            ->post(route('register.store'), $invalidRegistration)
            ->assertSessionHasErrors(['name', 'email', 'password']);
    }

    $this->withServerVariables(['REMOTE_ADDR' => $firstIp])
        ->post(route('register.store'), $invalidRegistration)
        ->assertTooManyRequests();

    $this->withServerVariables(['REMOTE_ADDR' => $secondIp])
        ->post(route('register.store'), $invalidRegistration)
        ->assertSessionHasErrors(['name', 'email', 'password']);

    $this->travel(15)->minutes();

    $this->withServerVariables(['REMOTE_ADDR' => $firstIp])
        ->post(route('register.store'), $invalidRegistration)
        ->assertSessionHasErrors(['name', 'email', 'password']);
});
