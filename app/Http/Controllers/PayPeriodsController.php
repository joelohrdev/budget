<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePayPeriodRequest;
use App\Http\Requests\UpdatePayPeriodRequest;
use App\Models\PayPeriod;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Inertia\Response;

class PayPeriodsController extends Controller
{
    public function index(): Response
    {
        $user = auth()->user();

        $payPeriods = $user->payPeriods()
            ->with(['cards.transactions' => function ($query) {
                $query->orderBy('transaction_date', 'desc');
            }, 'cards.transactions.category'])
            ->orderBy('start_date', 'desc')
            ->get()
            ->map(function ($payPeriod) {
                return [
                    'id' => $payPeriod->id,
                    'start_date' => $payPeriod->start_date->format('Y-m-d'),
                    'end_date' => $payPeriod->end_date->format('Y-m-d'),
                    'is_active' => $payPeriod->is_active,
                    'cards' => $payPeriod->cards->map(function ($card) {
                        return [
                            'id' => $card->id,
                            'name' => $card->name,
                            'type' => $card->type,
                            'budget_limit' => (float) $card->budget_limit,
                            'total_spent' => $card->totalSpent(),
                            'total_credits' => $card->totalCredits(),
                            'remaining_budget' => $card->remainingBudget(),
                            'transactions' => $card->transactions->map(function ($transaction) {
                                return [
                                    'id' => $transaction->id,
                                    'description' => $transaction->description,
                                    'amount' => (float) $transaction->amount,
                                    'type' => $transaction->type,
                                    'transaction_date' => $transaction->transaction_date->format('Y-m-d'),
                                    'category' => $transaction->category ? [
                                        'id' => $transaction->category->id,
                                        'name' => $transaction->category->name,
                                        'color' => $transaction->category->color,
                                    ] : null,
                                ];
                            }),
                        ];
                    }),
                ];
            });

        $categories = $user->categories()->orderBy('name')->get()->map(function ($category) {
            return [
                'id' => $category->id,
                'name' => $category->name,
                'color' => $category->color,
            ];
        });

        return Inertia::render('pay-periods/index', [
            'payPeriods' => $payPeriods,
            'categories' => $categories,
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('pay-periods/create');
    }

    public function store(StorePayPeriodRequest $request): RedirectResponse
    {
        $user = auth()->user();

        DB::transaction(function () use ($request, $user) {
            $user->payPeriods()->update(['is_active' => false]);

            $payPeriod = $user->payPeriods()->create([
                'start_date' => $request->validated('start_date'),
                'end_date' => $request->validated('end_date'),
                'is_active' => true,
            ]);

            $payPeriod->cards()->create([
                'user_id' => $user->id,
                'name' => 'Debit Card',
                'type' => 'debit',
                'budget_limit' => $request->validated('debit_card_budget'),
            ]);

            $payPeriod->cards()->create([
                'user_id' => $user->id,
                'name' => 'Credit Card',
                'type' => 'credit',
                'budget_limit' => $request->validated('credit_card_budget'),
            ]);
        });

        return redirect()->route('pay-periods.index')->with('success', 'Pay period created successfully.');
    }

    public function edit(PayPeriod $payPeriod): Response
    {
        if ($payPeriod->user_id !== auth()->id()) {
            abort(403);
        }

        $debitCard = $payPeriod->cards()->where('type', 'debit')->first();
        $creditCard = $payPeriod->cards()->where('type', 'credit')->first();

        return Inertia::render('pay-periods/edit', [
            'payPeriod' => [
                'id' => $payPeriod->id,
                'start_date' => $payPeriod->start_date->format('Y-m-d'),
                'end_date' => $payPeriod->end_date->format('Y-m-d'),
                'is_active' => $payPeriod->is_active,
                'debit_card_budget' => $debitCard ? (float) $debitCard->budget_limit : 0,
                'credit_card_budget' => $creditCard ? (float) $creditCard->budget_limit : 0,
                'debit_card_id' => $debitCard?->id,
                'credit_card_id' => $creditCard?->id,
            ],
        ]);
    }

    public function update(UpdatePayPeriodRequest $request, PayPeriod $payPeriod): RedirectResponse
    {
        if ($payPeriod->user_id !== auth()->id()) {
            abort(403);
        }

        DB::transaction(function () use ($request, $payPeriod) {
            $payPeriod->update([
                'start_date' => $request->validated('start_date'),
                'end_date' => $request->validated('end_date'),
            ]);

            $debitCard = $payPeriod->cards()->where('type', 'debit')->first();
            if ($debitCard) {
                $debitCard->update([
                    'budget_limit' => $request->validated('debit_card_budget'),
                ]);
            }

            $creditCard = $payPeriod->cards()->where('type', 'credit')->first();
            if ($creditCard) {
                $creditCard->update([
                    'budget_limit' => $request->validated('credit_card_budget'),
                ]);
            }
        });

        return redirect()->route('pay-periods.index')->with('success', 'Pay period updated successfully.');
    }
}
