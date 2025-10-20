<?php

use App\Http\Controllers\AnalyticsController;
use App\Http\Controllers\BillsController;
use App\Http\Controllers\CategoriesController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DebtCalculatorController;
use App\Http\Controllers\DebtPaymentsController;
use App\Http\Controllers\DebtsController;
use App\Http\Controllers\PayPeriodsController;
use App\Http\Controllers\TransactionsController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('welcome');
})->name('home');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('analytics', [AnalyticsController::class, 'index'])->name('analytics');

    Route::get('pay-periods', [PayPeriodsController::class, 'index'])->name('pay-periods.index');
    Route::get('pay-periods/create', [PayPeriodsController::class, 'create'])->name('pay-periods.create');
    Route::post('pay-periods', [PayPeriodsController::class, 'store'])->name('pay-periods.store');
    Route::get('pay-periods/{payPeriod}/edit', [PayPeriodsController::class, 'edit'])->name('pay-periods.edit');
    Route::put('pay-periods/{payPeriod}', [PayPeriodsController::class, 'update'])->name('pay-periods.update');

    Route::post('transactions', [TransactionsController::class, 'store'])->name('transactions.store');
    Route::put('transactions/{transaction}', [TransactionsController::class, 'update'])->name('transactions.update');
    Route::delete('transactions/{transaction}', [TransactionsController::class, 'destroy'])->name('transactions.destroy');

    Route::post('categories', [CategoriesController::class, 'store'])->name('categories.store');

    Route::get('bills', [BillsController::class, 'index'])->name('bills.index');
    Route::post('bills', [BillsController::class, 'store'])->name('bills.store');
    Route::put('bills/{bill}', [BillsController::class, 'update'])->name('bills.update');
    Route::delete('bills/{bill}', [BillsController::class, 'destroy'])->name('bills.destroy');

    Route::get('debts', [DebtsController::class, 'index'])->name('debts.index');
    Route::get('debts/{debt}', [DebtsController::class, 'show'])->name('debts.show');
    Route::post('debts', [DebtsController::class, 'store'])->name('debts.store');
    Route::put('debts/{debt}', [DebtsController::class, 'update'])->name('debts.update');
    Route::delete('debts/{debt}', [DebtsController::class, 'destroy'])->name('debts.destroy');

    Route::post('debts/{debt}/payments', [DebtPaymentsController::class, 'store'])->name('debts.payments.store');
    Route::delete('debts/{debt}/payments/{payment}', [DebtPaymentsController::class, 'destroy'])->name('debts.payments.destroy');

    Route::get('debt-calculator', [DebtCalculatorController::class, 'index'])->name('debt-calculator.index');
    Route::post('debts/{debt}/payoff-schedule', [DebtCalculatorController::class, 'payoffSchedule'])->name('debts.payoff-schedule');
    Route::post('debt-calculator/snowball', [DebtCalculatorController::class, 'snowballAnalysis'])->name('debt-calculator.snowball');
    Route::post('debt-calculator/avalanche', [DebtCalculatorController::class, 'avalancheAnalysis'])->name('debt-calculator.avalanche');
});

require __DIR__.'/settings.php';
require __DIR__.'/auth.php';
