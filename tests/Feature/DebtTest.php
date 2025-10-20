<?php

use App\Models\Debt;
use App\Models\User;

use function Pest\Laravel\actingAs;

test('user can view debts index', function () {
    $user = User::factory()->create();

    actingAs($user)
        ->get(route('debts.index'))
        ->assertSuccessful();
});

test('user can create a debt', function () {
    $user = User::factory()->create();

    actingAs($user)
        ->post(route('debts.store'), [
            'name' => 'Credit Card',
            'type' => 'credit_card',
            'principal_amount' => 5000.00,
            'current_balance' => 4500.00,
            'interest_rate' => 18.99,
            'minimum_payment' => 150.00,
            'start_date' => '2025-01-01',
        ])
        ->assertRedirect(route('debts.index'));

    expect(Debt::count())->toBe(1);

    $debt = Debt::first();
    expect($debt->user_id)->toBe($user->id);
    expect($debt->name)->toBe('Credit Card');
    expect($debt->type)->toBe('credit_card');
    expect((float) $debt->principal_amount)->toBe(5000.00);
    expect((float) $debt->current_balance)->toBe(4500.00);
    expect((float) $debt->interest_rate)->toBe(18.99);
});

test('debt requires valid data', function () {
    $user = User::factory()->create();

    actingAs($user)
        ->post(route('debts.store'), [
            'name' => '',
            'type' => 'invalid_type',
            'principal_amount' => -100,
            'current_balance' => -50,
            'interest_rate' => 150,
            'start_date' => 'invalid-date',
        ])
        ->assertSessionHasErrors(['name', 'type', 'principal_amount', 'current_balance', 'interest_rate', 'start_date']);
});

test('user can view debt details', function () {
    $user = User::factory()->create();
    $debt = Debt::factory()->create(['user_id' => $user->id]);

    actingAs($user)
        ->get(route('debts.show', $debt))
        ->assertSuccessful();
});

test('user cannot view another users debt', function () {
    $user1 = User::factory()->create();
    $user2 = User::factory()->create();
    $debt = Debt::factory()->create(['user_id' => $user1->id]);

    actingAs($user2)
        ->get(route('debts.show', $debt))
        ->assertForbidden();
});

test('user can delete their own debt', function () {
    $user = User::factory()->create();
    $debt = Debt::factory()->create(['user_id' => $user->id]);

    actingAs($user)
        ->delete(route('debts.destroy', $debt))
        ->assertRedirect(route('debts.index'));

    expect(Debt::count())->toBe(0);
});

test('user cannot delete another users debt', function () {
    $user1 = User::factory()->create();
    $user2 = User::factory()->create();
    $debt = Debt::factory()->create(['user_id' => $user1->id]);

    actingAs($user2)
        ->delete(route('debts.destroy', $debt))
        ->assertForbidden();

    expect(Debt::count())->toBe(1);
});

test('user can update their own debt', function () {
    $user = User::factory()->create();
    $debt = Debt::factory()->create([
        'user_id' => $user->id,
        'name' => 'Old Name',
        'current_balance' => 1000.00,
    ]);

    actingAs($user)
        ->put(route('debts.update', $debt), [
            'name' => 'New Name',
            'current_balance' => 900.00,
        ])
        ->assertRedirect();

    $debt->refresh();
    expect($debt->name)->toBe('New Name');
    expect((float) $debt->current_balance)->toBe(900.00);
});

test('user cannot update another users debt', function () {
    $user1 = User::factory()->create();
    $user2 = User::factory()->create();
    $debt = Debt::factory()->create([
        'user_id' => $user1->id,
        'name' => 'Original Name',
    ]);

    actingAs($user2)
        ->put(route('debts.update', $debt), [
            'name' => 'Hacked Name',
        ])
        ->assertForbidden();

    $debt->refresh();
    expect($debt->name)->toBe('Original Name');
});

test('debt calculates monthly interest correctly', function () {
    $debt = Debt::factory()->create([
        'current_balance' => 10000.00,
        'interest_rate' => 12.00,
    ]);

    $monthlyInterest = $debt->calculateMonthlyInterest();

    expect($monthlyInterest)->toBe(100.00);
});

test('debt generates payoff schedule', function () {
    $debt = Debt::factory()->create([
        'current_balance' => 1000.00,
        'interest_rate' => 12.00,
    ]);

    $schedule = $debt->generatePayoffSchedule(100);

    expect($schedule)->toBeArray();
    expect(count($schedule))->toBeGreaterThan(0);
    expect($schedule[0])->toHaveKeys(['payment_number', 'date', 'payment_amount', 'principal', 'interest', 'balance']);
});
