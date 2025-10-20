<?php

use App\Models\Debt;
use App\Models\User;

use function Pest\Laravel\actingAs;

test('user can view debt calculator', function () {
    $user = User::factory()->create();

    actingAs($user)
        ->get(route('debt-calculator.index'))
        ->assertSuccessful();
});

test('user can get payoff schedule for debt', function () {
    $user = User::factory()->create();
    $debt = Debt::factory()->create([
        'user_id' => $user->id,
        'current_balance' => 1000.00,
        'interest_rate' => 12.00,
    ]);

    $response = actingAs($user)
        ->post(route('debts.payoff-schedule', $debt), [
            'monthly_payment' => 100.00,
        ])
        ->assertSuccessful();

    $data = $response->json();
    expect($data)->toHaveKeys(['schedule', 'months', 'total_interest', 'total_paid']);
    expect($data['schedule'])->toBeArray();
    expect(count($data['schedule']))->toBeGreaterThan(0);
});

test('user cannot get payoff schedule for another users debt', function () {
    $user1 = User::factory()->create();
    $user2 = User::factory()->create();
    $debt = Debt::factory()->create(['user_id' => $user1->id]);

    actingAs($user2)
        ->post(route('debts.payoff-schedule', $debt), [
            'monthly_payment' => 100.00,
        ])
        ->assertForbidden();
});

test('payoff schedule requires valid monthly payment', function () {
    $user = User::factory()->create();
    $debt = Debt::factory()->create(['user_id' => $user->id]);

    actingAs($user)
        ->post(route('debts.payoff-schedule', $debt), [
            'monthly_payment' => -50,
        ])
        ->assertSessionHasErrors(['monthly_payment']);
});

test('user can get snowball analysis', function () {
    $user = User::factory()->create();
    Debt::factory()->count(3)->create([
        'user_id' => $user->id,
        'current_balance' => 1000.00,
        'interest_rate' => 15.00,
        'minimum_payment' => 50.00,
    ]);

    $response = actingAs($user)
        ->post(route('debt-calculator.snowball'), [
            'extra_payment' => 200.00,
        ])
        ->assertSuccessful();

    $data = $response->json();
    expect($data)->toHaveKeys(['strategy', 'total_months', 'total_interest', 'timeline']);
    expect($data['strategy'])->toBe('snowball');
    expect($data['timeline'])->toBeArray();
});

test('user can get avalanche analysis', function () {
    $user = User::factory()->create();
    Debt::factory()->count(3)->create([
        'user_id' => $user->id,
        'current_balance' => 1000.00,
        'interest_rate' => 15.00,
        'minimum_payment' => 50.00,
    ]);

    $response = actingAs($user)
        ->post(route('debt-calculator.avalanche'), [
            'extra_payment' => 200.00,
        ])
        ->assertSuccessful();

    $data = $response->json();
    expect($data)->toHaveKeys(['strategy', 'total_months', 'total_interest', 'timeline']);
    expect($data['strategy'])->toBe('avalanche');
    expect($data['timeline'])->toBeArray();
});

test('snowball analysis requires valid extra payment', function () {
    $user = User::factory()->create();

    actingAs($user)
        ->post(route('debt-calculator.snowball'), [
            'extra_payment' => -50,
        ])
        ->assertSessionHasErrors(['extra_payment']);
});

test('avalanche analysis requires valid extra payment', function () {
    $user = User::factory()->create();

    actingAs($user)
        ->post(route('debt-calculator.avalanche'), [
            'extra_payment' => 'invalid',
        ])
        ->assertSessionHasErrors(['extra_payment']);
});
