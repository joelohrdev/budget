<?php

use App\Models\Card;
use App\Models\Category;
use App\Models\PayPeriod;
use App\Models\Transaction;
use App\Models\User;
use App\Services\AnalyticsService;

use function Pest\Laravel\actingAs;

test('guests are redirected to the login page', function () {
    $this->get(route('analytics'))->assertRedirect(route('login'));
});

test('authenticated users can visit the analytics page', function () {
    $user = User::factory()->create();

    actingAs($user)
        ->get(route('analytics'))
        ->assertOk()
        ->assertInertia(fn ($page) => $page->component('analytics'));
});

test('analytics service calculates category spending correctly', function () {
    $user = User::factory()->create();
    $category1 = Category::factory()->create(['user_id' => $user->id, 'name' => 'Groceries']);
    $category2 = Category::factory()->create(['user_id' => $user->id, 'name' => 'Gas']);

    $payPeriod = PayPeriod::factory()->create(['user_id' => $user->id]);
    $card = Card::factory()->create([
        'user_id' => $user->id,
        'pay_period_id' => $payPeriod->id,
    ]);

    Transaction::factory()->create([
        'card_id' => $card->id,
        'category_id' => $category1->id,
        'amount' => 100.00,
        'type' => 'debit',
    ]);

    Transaction::factory()->create([
        'card_id' => $card->id,
        'category_id' => $category1->id,
        'amount' => 50.00,
        'type' => 'debit',
    ]);

    Transaction::factory()->create([
        'card_id' => $card->id,
        'category_id' => $category2->id,
        'amount' => 75.00,
        'type' => 'debit',
    ]);

    $analytics = new AnalyticsService($user);
    $spending = $analytics->getCategorySpending();

    expect($spending)->toHaveCount(2);
    expect($spending[0]['name'])->toBe('Groceries');
    expect($spending[0]['total'])->toBe(150.0);
    expect($spending[0]['transaction_count'])->toBe(2);
    expect($spending[0]['average'])->toBe(75.0);
    expect($spending[1]['name'])->toBe('Gas');
    expect($spending[1]['total'])->toBe(75.0);
});

test('analytics service excludes credits from spending calculations', function () {
    $user = User::factory()->create();
    $category = Category::factory()->create(['user_id' => $user->id]);

    $payPeriod = PayPeriod::factory()->create(['user_id' => $user->id]);
    $card = Card::factory()->create([
        'user_id' => $user->id,
        'pay_period_id' => $payPeriod->id,
    ]);

    Transaction::factory()->create([
        'card_id' => $card->id,
        'category_id' => $category->id,
        'amount' => 100.00,
        'type' => 'debit',
    ]);

    Transaction::factory()->credit()->create([
        'card_id' => $card->id,
        'category_id' => $category->id,
        'amount' => 25.00,
    ]);

    $analytics = new AnalyticsService($user);
    $spending = $analytics->getCategorySpending();

    expect($spending)->toHaveCount(1);
    expect($spending[0]['total'])->toBe(100.0);
});

test('analytics service calculates budget vs actual correctly', function () {
    $user = User::factory()->create();
    $category = Category::factory()->create(['user_id' => $user->id]);

    $payPeriod = PayPeriod::factory()->create([
        'user_id' => $user->id,
        'is_active' => true,
    ]);

    $card = Card::factory()->create([
        'user_id' => $user->id,
        'pay_period_id' => $payPeriod->id,
        'budget_limit' => 500.00,
        'name' => 'Test Card',
    ]);

    Transaction::factory()->create([
        'card_id' => $card->id,
        'category_id' => $category->id,
        'amount' => 200.00,
        'type' => 'debit',
    ]);

    $analytics = new AnalyticsService($user);
    $budgetVsActual = $analytics->getBudgetVsActual();

    expect($budgetVsActual)->toHaveCount(1);
    expect($budgetVsActual[0]['name'])->toBe('Test Card');
    expect($budgetVsActual[0]['budget_limit'])->toBe(500.0);
    expect($budgetVsActual[0]['spent'])->toBe(200.0);
    expect($budgetVsActual[0]['remaining'])->toBe(300.0);
    expect($budgetVsActual[0]['percent_used'])->toBe(40.0);
    expect($budgetVsActual[0]['over_budget'])->toBeFalse();
});

test('analytics service detects over budget spending', function () {
    $user = User::factory()->create();
    $category = Category::factory()->create(['user_id' => $user->id]);

    $payPeriod = PayPeriod::factory()->create([
        'user_id' => $user->id,
        'is_active' => true,
    ]);

    $card = Card::factory()->create([
        'user_id' => $user->id,
        'pay_period_id' => $payPeriod->id,
        'budget_limit' => 100.00,
    ]);

    Transaction::factory()->create([
        'card_id' => $card->id,
        'category_id' => $category->id,
        'amount' => 150.00,
        'type' => 'debit',
    ]);

    $analytics = new AnalyticsService($user);
    $budgetVsActual = $analytics->getBudgetVsActual();

    expect($budgetVsActual[0]['over_budget'])->toBeTrue();
    expect($budgetVsActual[0]['percent_used'])->toBe(150.0);
});

test('analytics service calculates spending summary correctly', function () {
    $user = User::factory()->create();
    $category = Category::factory()->create(['user_id' => $user->id]);

    $payPeriod = PayPeriod::factory()->create(['user_id' => $user->id]);
    $card = Card::factory()->create([
        'user_id' => $user->id,
        'pay_period_id' => $payPeriod->id,
    ]);

    Transaction::factory()->create([
        'card_id' => $card->id,
        'category_id' => $category->id,
        'amount' => 100.00,
        'type' => 'debit',
    ]);

    Transaction::factory()->create([
        'card_id' => $card->id,
        'category_id' => $category->id,
        'amount' => 50.00,
        'type' => 'debit',
    ]);

    Transaction::factory()->credit()->create([
        'card_id' => $card->id,
        'category_id' => $category->id,
        'amount' => 25.00,
    ]);

    $analytics = new AnalyticsService($user);
    $summary = $analytics->getSpendingSummary();

    expect($summary['total_spent'])->toBe(150.0);
    expect($summary['total_credits'])->toBe(25.0);
    expect($summary['net_spending'])->toBe(125.0);
    expect($summary['transaction_count'])->toBe(3);
    expect($summary['average_transaction'])->toBe(50.0);
});

test('analytics service returns top spending categories', function () {
    $user = User::factory()->create();

    $payPeriod = PayPeriod::factory()->create(['user_id' => $user->id]);
    $card = Card::factory()->create([
        'user_id' => $user->id,
        'pay_period_id' => $payPeriod->id,
    ]);

    $categoryNames = ['Cat1', 'Cat2', 'Cat3', 'Cat4', 'Cat5', 'Cat6', 'Cat7', 'Cat8', 'Cat9', 'Cat10'];
    foreach ($categoryNames as $index => $name) {
        $category = Category::factory()->create([
            'user_id' => $user->id,
            'name' => $name,
        ]);

        Transaction::factory()->create([
            'card_id' => $card->id,
            'category_id' => $category->id,
            'amount' => ($index + 1) * 10,
            'type' => 'debit',
        ]);
    }

    $analytics = new AnalyticsService($user);
    $topCategories = $analytics->getTopCategories(5);

    expect($topCategories)->toHaveCount(5);
    expect($topCategories[0]['total'])->toBe(100.0);
    expect($topCategories[4]['total'])->toBe(60.0);
});

test('analytics service only shows user own transactions', function () {
    $user1 = User::factory()->create();
    $user2 = User::factory()->create();

    $category1 = Category::factory()->create(['user_id' => $user1->id]);
    $category2 = Category::factory()->create(['user_id' => $user2->id]);

    $payPeriod1 = PayPeriod::factory()->create(['user_id' => $user1->id]);
    $payPeriod2 = PayPeriod::factory()->create(['user_id' => $user2->id]);

    $card1 = Card::factory()->create([
        'user_id' => $user1->id,
        'pay_period_id' => $payPeriod1->id,
    ]);

    $card2 = Card::factory()->create([
        'user_id' => $user2->id,
        'pay_period_id' => $payPeriod2->id,
    ]);

    Transaction::factory()->create([
        'card_id' => $card1->id,
        'category_id' => $category1->id,
        'amount' => 100.00,
        'type' => 'debit',
    ]);

    Transaction::factory()->create([
        'card_id' => $card2->id,
        'category_id' => $category2->id,
        'amount' => 200.00,
        'type' => 'debit',
    ]);

    $analytics1 = new AnalyticsService($user1);
    $analytics2 = new AnalyticsService($user2);

    $summary1 = $analytics1->getSpendingSummary();
    $summary2 = $analytics2->getSpendingSummary();

    expect($summary1['total_spent'])->toBe(100.0);
    expect($summary2['total_spent'])->toBe(200.0);
});

test('analytics page returns empty data when no transactions exist', function () {
    $user = User::factory()->create();

    actingAs($user)
        ->get(route('analytics'))
        ->assertOk()
        ->assertInertia(fn ($page) => $page
            ->has('summary')
            ->has('categorySpending')
            ->has('topCategories')
            ->has('budgetVsActual')
            ->has('spendingTrends')
            ->has('monthOverMonth')
            ->where('categorySpending', [])
            ->where('topCategories', [])
        );
});

test('analytics service respects date range filters', function () {
    $user = User::factory()->create();
    $category = Category::factory()->create(['user_id' => $user->id]);

    $payPeriod = PayPeriod::factory()->create(['user_id' => $user->id]);
    $card = Card::factory()->create([
        'user_id' => $user->id,
        'pay_period_id' => $payPeriod->id,
    ]);

    Transaction::factory()->create([
        'card_id' => $card->id,
        'category_id' => $category->id,
        'amount' => 100.00,
        'type' => 'debit',
        'transaction_date' => '2025-01-15',
    ]);

    Transaction::factory()->create([
        'card_id' => $card->id,
        'category_id' => $category->id,
        'amount' => 50.00,
        'type' => 'debit',
        'transaction_date' => '2025-02-15',
    ]);

    $analytics = new AnalyticsService($user);
    $januarySpending = $analytics->getSpendingSummary('2025-01-01', '2025-01-31');
    $februarySpending = $analytics->getSpendingSummary('2025-02-01', '2025-02-28');

    expect($januarySpending['total_spent'])->toBe(100.0);
    expect($februarySpending['total_spent'])->toBe(50.0);
});
