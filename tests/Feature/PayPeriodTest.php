<?php

use App\Models\Card;
use App\Models\PayPeriod;
use App\Models\User;

use function Pest\Laravel\actingAs;

test('user can view pay periods index', function () {
    $user = User::factory()->create();
    $payPeriod = PayPeriod::factory()->create(['user_id' => $user->id]);

    actingAs($user)
        ->get(route('pay-periods.index'))
        ->assertSuccessful();
});

test('user can view create pay period page', function () {
    $user = User::factory()->create();

    actingAs($user)
        ->get(route('pay-periods.create'))
        ->assertSuccessful();
});

test('user can create a pay period', function () {
    $user = User::factory()->create();

    actingAs($user)
        ->post(route('pay-periods.store'), [
            'start_date' => '2025-10-17',
            'end_date' => '2025-10-30',
            'debit_card_budget' => 1000.00,
            'credit_card_budget' => 500.00,
        ])
        ->assertRedirect(route('pay-periods.index'));

    expect(PayPeriod::count())->toBe(1);
    expect(Card::count())->toBe(2);

    $payPeriod = PayPeriod::first();
    expect($payPeriod->user_id)->toBe($user->id);
    expect($payPeriod->is_active)->toBeTrue();
    expect($payPeriod->cards)->toHaveCount(2);
});

test('creating a new pay period deactivates previous active period', function () {
    $user = User::factory()->create();
    $oldPayPeriod = PayPeriod::factory()
        ->active()
        ->create(['user_id' => $user->id]);

    actingAs($user)
        ->post(route('pay-periods.store'), [
            'start_date' => '2025-10-17',
            'end_date' => '2025-10-30',
            'debit_card_budget' => 1000.00,
            'credit_card_budget' => 500.00,
        ]);

    $oldPayPeriod->refresh();
    expect($oldPayPeriod->is_active)->toBeFalse();

    $newPayPeriod = PayPeriod::where('id', '!=', $oldPayPeriod->id)->first();
    expect($newPayPeriod->is_active)->toBeTrue();
});

test('pay period requires valid dates', function () {
    $user = User::factory()->create();

    actingAs($user)
        ->post(route('pay-periods.store'), [
            'start_date' => '2025-10-30',
            'end_date' => '2025-10-17',
            'debit_card_budget' => 1000.00,
            'credit_card_budget' => 500.00,
        ])
        ->assertSessionHasErrors(['start_date', 'end_date']);
});

test('pay period requires positive budgets', function () {
    $user = User::factory()->create();

    actingAs($user)
        ->post(route('pay-periods.store'), [
            'start_date' => '2025-10-17',
            'end_date' => '2025-10-30',
            'debit_card_budget' => -100,
            'credit_card_budget' => -50,
        ])
        ->assertSessionHasErrors(['debit_card_budget', 'credit_card_budget']);
});

test('user can view edit pay period page', function () {
    $user = User::factory()->create();
    $payPeriod = PayPeriod::factory()->create(['user_id' => $user->id]);

    actingAs($user)
        ->get(route('pay-periods.edit', $payPeriod))
        ->assertSuccessful();
});

test('user can update a pay period', function () {
    $user = User::factory()->create();
    $payPeriod = PayPeriod::factory()->create([
        'user_id' => $user->id,
        'start_date' => '2025-10-17',
        'end_date' => '2025-10-30',
    ]);

    $debitCard = Card::factory()->create([
        'user_id' => $user->id,
        'pay_period_id' => $payPeriod->id,
        'type' => 'debit',
        'budget_limit' => 1000.00,
    ]);

    $creditCard = Card::factory()->create([
        'user_id' => $user->id,
        'pay_period_id' => $payPeriod->id,
        'type' => 'credit',
        'budget_limit' => 500.00,
    ]);

    actingAs($user)
        ->put(route('pay-periods.update', $payPeriod), [
            'start_date' => '2025-10-18',
            'end_date' => '2025-10-31',
            'debit_card_budget' => 1200.00,
            'credit_card_budget' => 600.00,
        ])
        ->assertRedirect(route('pay-periods.index'));

    $payPeriod->refresh();
    expect($payPeriod->start_date->format('Y-m-d'))->toBe('2025-10-18');
    expect($payPeriod->end_date->format('Y-m-d'))->toBe('2025-10-31');

    $debitCard->refresh();
    expect((float) $debitCard->budget_limit)->toBe(1200.00);

    $creditCard->refresh();
    expect((float) $creditCard->budget_limit)->toBe(600.00);
});

test('user cannot edit another users pay period', function () {
    $user1 = User::factory()->create();
    $user2 = User::factory()->create();
    $payPeriod = PayPeriod::factory()->create(['user_id' => $user1->id]);

    actingAs($user2)
        ->get(route('pay-periods.edit', $payPeriod))
        ->assertForbidden();
});

test('user cannot update another users pay period', function () {
    $user1 = User::factory()->create();
    $user2 = User::factory()->create();
    $payPeriod = PayPeriod::factory()->create(['user_id' => $user1->id]);

    actingAs($user2)
        ->put(route('pay-periods.update', $payPeriod), [
            'start_date' => '2025-10-18',
            'end_date' => '2025-10-31',
            'debit_card_budget' => 1200.00,
            'credit_card_budget' => 600.00,
        ])
        ->assertForbidden();
});
