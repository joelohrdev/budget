<?php

use App\Models\Bill;
use App\Models\User;

use function Pest\Laravel\actingAs;

test('user can view bills index', function () {
    $user = User::factory()->create();

    actingAs($user)
        ->get(route('bills.index'))
        ->assertSuccessful();
});

test('user can create a bill', function () {
    $user = User::factory()->create();

    actingAs($user)
        ->post(route('bills.store'), [
            'name' => 'Rent',
            'amount' => 1500.00,
            'due_date' => '2025-11-01',
        ])
        ->assertRedirect();

    expect(Bill::count())->toBe(1);

    $bill = Bill::first();
    expect($bill->user_id)->toBe($user->id);
    expect($bill->name)->toBe('Rent');
    expect((float) $bill->amount)->toBe(1500.00);
    expect($bill->due_date->format('Y-m-d'))->toBe('2025-11-01');
});

test('bill requires valid data', function () {
    $user = User::factory()->create();

    actingAs($user)
        ->post(route('bills.store'), [
            'name' => '',
            'amount' => -100,
            'due_date' => 'invalid-date',
        ])
        ->assertSessionHasErrors(['name', 'amount', 'due_date']);
});

test('user can delete their own bill', function () {
    $user = User::factory()->create();
    $bill = Bill::factory()->create(['user_id' => $user->id]);

    actingAs($user)
        ->delete(route('bills.destroy', $bill))
        ->assertRedirect();

    expect(Bill::count())->toBe(0);
});

test('user cannot delete another users bill', function () {
    $user1 = User::factory()->create();
    $user2 = User::factory()->create();
    $bill = Bill::factory()->create(['user_id' => $user1->id]);

    actingAs($user2)
        ->delete(route('bills.destroy', $bill))
        ->assertForbidden();

    expect(Bill::count())->toBe(1);
});

test('user can update their own bill', function () {
    $user = User::factory()->create();
    $bill = Bill::factory()->create([
        'user_id' => $user->id,
        'name' => 'Old Name',
        'amount' => 100.00,
        'due_date' => '2025-11-01',
    ]);

    actingAs($user)
        ->put(route('bills.update', $bill), [
            'name' => 'New Name',
            'amount' => 200.00,
            'due_date' => '2025-11-15',
        ])
        ->assertRedirect();

    $bill->refresh();
    expect($bill->name)->toBe('New Name');
    expect((float) $bill->amount)->toBe(200.00);
    expect($bill->due_date->format('Y-m-d'))->toBe('2025-11-15');
});

test('user cannot update another users bill', function () {
    $user1 = User::factory()->create();
    $user2 = User::factory()->create();
    $bill = Bill::factory()->create([
        'user_id' => $user1->id,
        'name' => 'Original Name',
    ]);

    actingAs($user2)
        ->put(route('bills.update', $bill), [
            'name' => 'Hacked Name',
            'amount' => 999.99,
            'due_date' => '2025-12-31',
        ])
        ->assertForbidden();

    $bill->refresh();
    expect($bill->name)->toBe('Original Name');
});

test('bill update requires valid data', function () {
    $user = User::factory()->create();
    $bill = Bill::factory()->create(['user_id' => $user->id]);

    actingAs($user)
        ->put(route('bills.update', $bill), [
            'name' => '',
            'amount' => -100,
            'due_date' => 'invalid-date',
        ])
        ->assertSessionHasErrors(['name', 'amount', 'due_date']);
});
