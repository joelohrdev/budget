<?php

use App\Http\Controllers\CategoriesController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\PayPeriodsController;
use App\Http\Controllers\TransactionsController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('welcome');
})->name('home');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::get('pay-periods', [PayPeriodsController::class, 'index'])->name('pay-periods.index');
    Route::get('pay-periods/create', [PayPeriodsController::class, 'create'])->name('pay-periods.create');
    Route::post('pay-periods', [PayPeriodsController::class, 'store'])->name('pay-periods.store');
    Route::get('pay-periods/{payPeriod}/edit', [PayPeriodsController::class, 'edit'])->name('pay-periods.edit');
    Route::put('pay-periods/{payPeriod}', [PayPeriodsController::class, 'update'])->name('pay-periods.update');

    Route::post('transactions', [TransactionsController::class, 'store'])->name('transactions.store');
    Route::delete('transactions/{transaction}', [TransactionsController::class, 'destroy'])->name('transactions.destroy');

    Route::post('categories', [CategoriesController::class, 'store'])->name('categories.store');
});

require __DIR__.'/settings.php';
require __DIR__.'/auth.php';
