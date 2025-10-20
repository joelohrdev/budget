<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreDebtPaymentRequest;
use App\Models\Debt;
use App\Models\DebtPayment;
use Illuminate\Http\RedirectResponse;

class DebtPaymentsController extends Controller
{
    public function store(StoreDebtPaymentRequest $request, Debt $debt): RedirectResponse
    {
        if ($debt->user_id !== auth()->id()) {
            abort(403);
        }

        $validated = $request->validated();

        $debt->payments()->create($validated);

        $newBalance = $debt->current_balance - $validated['principal_amount'];
        $debt->update(['current_balance' => max(0, $newBalance)]);

        return back();
    }

    public function destroy(Debt $debt, DebtPayment $payment): RedirectResponse
    {
        if ($debt->user_id !== auth()->id() || $payment->debt_id !== $debt->id) {
            abort(403);
        }

        $newBalance = $debt->current_balance + $payment->principal_amount;
        $debt->update(['current_balance' => $newBalance]);

        $payment->delete();

        return back();
    }
}
