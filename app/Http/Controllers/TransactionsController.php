<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreTransactionRequest;
use App\Http\Requests\UpdateTransactionRequest;
use App\Models\Card;
use App\Models\Transaction;
use Illuminate\Http\RedirectResponse;

class TransactionsController extends Controller
{
    public function store(StoreTransactionRequest $request): RedirectResponse
    {
        $card = Card::findOrFail($request->validated('card_id'));

        if ($card->user_id !== auth()->id()) {
            abort(403);
        }

        $card->transactions()->create($request->validated());

        return redirect()->route('pay-periods.index')->with('success', 'Transaction added successfully.');
    }

    public function update(UpdateTransactionRequest $request, Transaction $transaction): RedirectResponse
    {
        $transaction->update($request->validated());

        return redirect()->route('pay-periods.index')->with('success', 'Transaction updated successfully.');
    }

    public function destroy(Transaction $transaction): RedirectResponse
    {
        if ($transaction->card->user_id !== auth()->id()) {
            abort(403);
        }

        $transaction->delete();

        return redirect()->route('pay-periods.index')->with('success', 'Transaction deleted successfully.');
    }
}
