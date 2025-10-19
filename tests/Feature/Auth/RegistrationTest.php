<?php

use App\Models\User;

test('registration screen can be rendered when no users exist', function () {
    $response = $this->get(route('register'));

    $response->assertStatus(200);
});

test('new users can register when no users exist', function () {
    $response = $this->post(route('register.store'), [
        'name' => 'Test User',
        'email' => 'test@example.com',
        'password' => 'password',
        'password_confirmation' => 'password',
    ]);

    $this->assertAuthenticated();
    $response->assertRedirect(route('dashboard', absolute: false));
});

test('registration screen cannot be accessed after first user is created', function () {
    User::factory()->create();

    $response = $this->get(route('register'));

    $response->assertForbidden();
});

test('new users cannot register after first user is created', function () {
    User::factory()->create();

    $response = $this->post(route('register.store'), [
        'name' => 'Test User',
        'email' => 'test@example.com',
        'password' => 'password',
        'password_confirmation' => 'password',
    ]);

    $response->assertForbidden();
    $this->assertGuest();
});
