<?php

use App\Models\User;

use function Pest\Laravel\actingAs;
use function Pest\Laravel\assertDatabaseHas;

test('user can create a category', function () {
    $user = User::factory()->create();

    actingAs($user)
        ->post('/categories', [
            'name' => 'Groceries',
            'color' => '#10b981',
        ])
        ->assertRedirect();

    assertDatabaseHas('categories', [
        'user_id' => $user->id,
        'name' => 'Groceries',
        'color' => '#10b981',
    ]);
});

test('user can create a category without color', function () {
    $user = User::factory()->create();

    actingAs($user)
        ->post('/categories', [
            'name' => 'Dining',
        ])
        ->assertRedirect();

    assertDatabaseHas('categories', [
        'user_id' => $user->id,
        'name' => 'Dining',
    ]);
});

test('category requires a name', function () {
    $user = User::factory()->create();

    actingAs($user)
        ->post('/categories', [])
        ->assertSessionHasErrors(['name']);
});

test('category name must be unique per user', function () {
    $user = User::factory()->create();

    $user->categories()->create([
        'name' => 'Groceries',
        'color' => '#10b981',
    ]);

    actingAs($user)
        ->post('/categories', [
            'name' => 'Groceries',
            'color' => '#ef4444',
        ])
        ->assertSessionHasErrors(['name']);
});

test('different users can have categories with the same name', function () {
    $user1 = User::factory()->create();
    $user2 = User::factory()->create();

    $user1->categories()->create([
        'name' => 'Groceries',
        'color' => '#10b981',
    ]);

    actingAs($user2)
        ->post('/categories', [
            'name' => 'Groceries',
            'color' => '#ef4444',
        ])
        ->assertRedirect();

    assertDatabaseHas('categories', [
        'user_id' => $user2->id,
        'name' => 'Groceries',
    ]);
});
