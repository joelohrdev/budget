<?php

use App\Models\Card;
use App\Models\PayPeriod;
use App\Models\Transaction;
use App\Models\User;

use function Pest\Laravel\actingAs;

test('user can add a transaction to their card', function () {
    $user = User::factory()->create();
    $payPeriod = PayPeriod::factory()->create(['user_id' => $user->id]);
    $card = Card::factory()->create([
        'user_id' => $user->id,
        'pay_period_id' => $payPeriod->id,
    ]);

    actingAs($user)
        ->post(route('transactions.store'), [
            'card_id' => $card->id,
            'description' => 'Grocery Store',
            'amount' => 50.00,
            'type' => 'debit',
            'transaction_date' => '2025-10-19',
        ])
        ->assertRedirect(route('pay-periods.index'));

    expect(Transaction::count())->toBe(1);

    $transaction = Transaction::first();
    expect($transaction->card_id)->toBe($card->id);
    expect($transaction->description)->toBe('Grocery Store');
    expect((float) $transaction->amount)->toBe(50.00);
});

test('user cannot add transaction to another users card', function () {
    $user = User::factory()->create();
    $otherUser = User::factory()->create();
    $payPeriod = PayPeriod::factory()->create(['user_id' => $otherUser->id]);
    $card = Card::factory()->create([
        'user_id' => $otherUser->id,
        'pay_period_id' => $payPeriod->id,
    ]);

    actingAs($user)
        ->post(route('transactions.store'), [
            'card_id' => $card->id,
            'description' => 'Grocery Store',
            'amount' => 50.00,
            'type' => 'debit',
            'transaction_date' => '2025-10-19',
        ])
        ->assertForbidden();
});

test('user can delete their transaction', function () {
    $user = User::factory()->create();
    $payPeriod = PayPeriod::factory()->create(['user_id' => $user->id]);
    $card = Card::factory()->create([
        'user_id' => $user->id,
        'pay_period_id' => $payPeriod->id,
    ]);
    $transaction = Transaction::factory()->create(['card_id' => $card->id]);

    actingAs($user)
        ->delete(route('transactions.destroy', $transaction))
        ->assertRedirect(route('pay-periods.index'));

    expect(Transaction::count())->toBe(0);
});

test('user cannot delete another users transaction', function () {
    $user = User::factory()->create();
    $otherUser = User::factory()->create();
    $payPeriod = PayPeriod::factory()->create(['user_id' => $otherUser->id]);
    $card = Card::factory()->create([
        'user_id' => $otherUser->id,
        'pay_period_id' => $payPeriod->id,
    ]);
    $transaction = Transaction::factory()->create(['card_id' => $card->id]);

    actingAs($user)
        ->delete(route('transactions.destroy', $transaction))
        ->assertForbidden();
});

test('user can update their transaction', function () {
    $user = User::factory()->create();
    $payPeriod = PayPeriod::factory()->create(['user_id' => $user->id]);
    $card = Card::factory()->create([
        'user_id' => $user->id,
        'pay_period_id' => $payPeriod->id,
    ]);

    $transaction = $card->transactions()->create([
        'description' => 'Original Description',
        'amount' => 50.00,
        'type' => 'debit',
        'transaction_date' => now(),
    ]);

    actingAs($user)
        ->put(route('transactions.update', $transaction), [
            'card_id' => $card->id,
            'description' => 'Updated Description',
            'amount' => 75.00,
            'type' => 'credit',
            'transaction_date' => now()->format('Y-m-d'),
        ])
        ->assertRedirect(route('pay-periods.index'));

    $transaction->refresh();
    expect($transaction->description)->toBe('Updated Description');
    expect((float) $transaction->amount)->toBe(75.00);
    expect($transaction->type)->toBe('credit');
});

test('user cannot update another users transaction', function () {
    $user1 = User::factory()->create();
    $user2 = User::factory()->create();
    $payPeriod = PayPeriod::factory()->create(['user_id' => $user1->id]);
    $card = Card::factory()->create([
        'user_id' => $user1->id,
        'pay_period_id' => $payPeriod->id,
    ]);

    $transaction = $card->transactions()->create([
        'description' => 'Original Description',
        'amount' => 50.00,
        'type' => 'debit',
        'transaction_date' => now(),
    ]);

    actingAs($user2)
        ->put(route('transactions.update', $transaction), [
            'card_id' => $card->id,
            'description' => 'Hacked Description',
            'amount' => 999.00,
            'type' => 'debit',
            'transaction_date' => now()->format('Y-m-d'),
        ])
        ->assertForbidden();

    $transaction->refresh();
    expect($transaction->description)->toBe('Original Description');
});

test('transaction requires valid data', function () {
    $user = User::factory()->create();
    $payPeriod = PayPeriod::factory()->create(['user_id' => $user->id]);
    $card = Card::factory()->create([
        'user_id' => $user->id,
        'pay_period_id' => $payPeriod->id,
    ]);

    actingAs($user)
        ->post(route('transactions.store'), [
            'card_id' => $card->id,
            'description' => '',
            'amount' => -50.00,
            'type' => 'invalid',
            'transaction_date' => 'not-a-date',
        ])
        ->assertSessionHasErrors(['description', 'amount', 'type', 'transaction_date']);
});

test('card budget calculations work correctly', function () {
    $user = User::factory()->create();
    $payPeriod = PayPeriod::factory()->create(['user_id' => $user->id]);
    $card = Card::factory()->create([
        'user_id' => $user->id,
        'pay_period_id' => $payPeriod->id,
        'budget_limit' => 1000.00,
    ]);

    Transaction::factory()->create([
        'card_id' => $card->id,
        'amount' => 100.00,
        'type' => 'debit',
    ]);

    Transaction::factory()->create([
        'card_id' => $card->id,
        'amount' => 50.00,
        'type' => 'debit',
    ]);

    Transaction::factory()->credit()->create([
        'card_id' => $card->id,
        'amount' => 25.00,
    ]);

    expect($card->totalSpent())->toBe(150.0);
    expect($card->totalCredits())->toBe(25.0);
    expect($card->remainingBudget())->toBe(875.0);
});
