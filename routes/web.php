<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {
    Route::get('/dashboard', function () { return view('dashboard'); })->name('dashboard');
    Route::get('bills', \App\Livewire\Bill\Index::class)->name('bill.index');
    Route::get('bills/create', \App\Livewire\Bill\Create::class)->name('bill.create');
    Route::get('bills/{bill:slug}', \App\Livewire\Bill\Show::class)->name('bill.show');

    Route::get('bills/{bill:slug}/payments/create', \App\Livewire\Payment\Create::class)->name('payment.create');
});
