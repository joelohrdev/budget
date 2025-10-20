<?php

use App\Models\Debt;
use App\Models\DebtPayment;
use App\Models\User;

use function Pest\Laravel\actingAs;

test('user can add payment to their debt', function () {
    $user = User::factory()->create();
    $debt = Debt::factory()->create([
        'user_id' => $user->id,
        'current_balance' => 1000.00,
    ]);

    actingAs($user)
        ->post(route('debts.payments.store', $debt), [
            'amount' => 150.00,
            'principal_amount' => 130.00,
            'interest_amount' => 20.00,
            'payment_date' => '2025-01-15',
        ])
        ->assertRedirect();

    expect(DebtPayment::count())->toBe(1);

    $payment = DebtPayment::first();
    expect($payment->debt_id)->toBe($debt->id);
    expect((float) $payment->amount)->toBe(150.00);
    expect((float) $payment->principal_amount)->toBe(130.00);
    expect((float) $payment->interest_amount)->toBe(20.00);
});

test('payment updates debt balance', function () {
    $user = User::factory()->create();
    $debt = Debt::factory()->create([
        'user_id' => $user->id,
        'current_balance' => 1000.00,
    ]);

    actingAs($user)
        ->post(route('debts.payments.store', $debt), [
            'amount' => 150.00,
            'principal_amount' => 130.00,
            'interest_amount' => 20.00,
            'payment_date' => '2025-01-15',
        ])
        ->assertRedirect();

    $debt->refresh();
    expect((float) $debt->current_balance)->toBe(870.00);
});

test('payment requires valid data', function () {
    $user = User::factory()->create();
    $debt = Debt::factory()->create(['user_id' => $user->id]);

    actingAs($user)
        ->post(route('debts.payments.store', $debt), [
            'amount' => -50,
            'principal_amount' => 'invalid',
            'interest_amount' => -10,
            'payment_date' => 'invalid-date',
        ])
        ->assertSessionHasErrors(['amount', 'principal_amount', 'interest_amount', 'payment_date']);
});

test('user cannot add payment to another users debt', function () {
    $user1 = User::factory()->create();
    $user2 = User::factory()->create();
    $debt = Debt::factory()->create(['user_id' => $user1->id]);

    actingAs($user2)
        ->post(route('debts.payments.store', $debt), [
            'amount' => 150.00,
            'principal_amount' => 130.00,
            'interest_amount' => 20.00,
            'payment_date' => '2025-01-15',
        ])
        ->assertForbidden();

    expect(DebtPayment::count())->toBe(0);
});

test('user can delete their debt payment', function () {
    $user = User::factory()->create();
    $debt = Debt::factory()->create([
        'user_id' => $user->id,
        'current_balance' => 1000.00,
    ]);
    $payment = DebtPayment::factory()->create([
        'debt_id' => $debt->id,
        'principal_amount' => 100.00,
    ]);

    actingAs($user)
        ->delete(route('debts.payments.destroy', [$debt, $payment]))
        ->assertRedirect();

    expect(DebtPayment::count())->toBe(0);
});

test('deleting payment restores debt balance', function () {
    $user = User::factory()->create();
    $debt = Debt::factory()->create([
        'user_id' => $user->id,
        'current_balance' => 900.00,
    ]);
    $payment = DebtPayment::factory()->create([
        'debt_id' => $debt->id,
        'principal_amount' => 100.00,
    ]);

    actingAs($user)
        ->delete(route('debts.payments.destroy', [$debt, $payment]))
        ->assertRedirect();

    $debt->refresh();
    expect((float) $debt->current_balance)->toBe(1000.00);
});

test('user cannot delete another users payment', function () {
    $user1 = User::factory()->create();
    $user2 = User::factory()->create();
    $debt = Debt::factory()->create(['user_id' => $user1->id]);
    $payment = DebtPayment::factory()->create(['debt_id' => $debt->id]);

    actingAs($user2)
        ->delete(route('debts.payments.destroy', [$debt, $payment]))
        ->assertForbidden();

    expect(DebtPayment::count())->toBe(1);
});
