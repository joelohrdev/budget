<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreDebtRequest;
use App\Http\Requests\UpdateDebtRequest;
use App\Models\Debt;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class DebtsController extends Controller
{
    public function index(): Response
    {
        $user = auth()->user();

        $debts = $user->debts()
            ->with('payments')
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function ($debt) {
                $totalPaid = $debt->payments->sum('principal_amount');
                $totalInterestPaid = $debt->payments->sum('interest_amount');

                return [
                    'id' => $debt->id,
                    'name' => $debt->name,
                    'type' => $debt->type,
                    'principal_amount' => $debt->principal_amount ? (float) $debt->principal_amount : null,
                    'current_balance' => (float) $debt->current_balance,
                    'interest_rate' => (float) $debt->interest_rate,
                    'minimum_payment' => $debt->minimum_payment ? (float) $debt->minimum_payment : null,
                    'term_months' => $debt->term_months,
                    'start_date' => $debt->start_date->format('Y-m-d'),
                    'payoff_target_date' => $debt->payoff_target_date?->format('Y-m-d'),
                    'notes' => $debt->notes,
                    'monthly_interest' => $debt->calculateMonthlyInterest(),
                    'total_paid' => (float) $totalPaid,
                    'total_interest_paid' => (float) $totalInterestPaid,
                    'progress_percentage' => $debt->principal_amount && $debt->principal_amount > 0
                        ? round((($debt->principal_amount - $debt->current_balance) / $debt->principal_amount) * 100, 2)
                        : null,
                ];
            });

        $summary = [
            'total_debt' => $debts->sum('current_balance'),
            'total_monthly_interest' => $debts->sum('monthly_interest'),
            'total_principal' => $debts->sum('principal_amount'),
            'total_paid' => $debts->sum('total_paid'),
        ];

        return Inertia::render('debts/index', [
            'debts' => $debts,
            'summary' => $summary,
        ]);
    }

    public function show(Debt $debt): Response
    {
        if ($debt->user_id !== auth()->id()) {
            abort(403);
        }

        $debt->load('payments');

        $payments = $debt->payments()
            ->orderBy('payment_date', 'desc')
            ->get()
            ->map(function ($payment) {
                return [
                    'id' => $payment->id,
                    'amount' => (float) $payment->amount,
                    'principal_amount' => (float) $payment->principal_amount,
                    'interest_amount' => (float) $payment->interest_amount,
                    'payment_date' => $payment->payment_date->format('Y-m-d'),
                    'notes' => $payment->notes,
                ];
            });

        return Inertia::render('debts/show', [
            'debt' => [
                'id' => $debt->id,
                'name' => $debt->name,
                'type' => $debt->type,
                'principal_amount' => $debt->principal_amount ? (float) $debt->principal_amount : null,
                'current_balance' => (float) $debt->current_balance,
                'interest_rate' => (float) $debt->interest_rate,
                'minimum_payment' => $debt->minimum_payment ? (float) $debt->minimum_payment : null,
                'term_months' => $debt->term_months,
                'start_date' => $debt->start_date->format('Y-m-d'),
                'payoff_target_date' => $debt->payoff_target_date?->format('Y-m-d'),
                'notes' => $debt->notes,
                'monthly_interest' => $debt->calculateMonthlyInterest(),
            ],
            'payments' => $payments,
        ]);
    }

    public function store(StoreDebtRequest $request): RedirectResponse
    {
        auth()->user()->debts()->create($request->validated());

        return redirect()->route('debts.index');
    }

    public function update(UpdateDebtRequest $request, Debt $debt): RedirectResponse
    {
        if ($debt->user_id !== auth()->id()) {
            abort(403);
        }

        $debt->update($request->validated());

        return back();
    }

    public function destroy(Debt $debt): RedirectResponse
    {
        if ($debt->user_id !== auth()->id()) {
            abort(403);
        }

        $debt->delete();

        return redirect()->route('debts.index');
    }
}
