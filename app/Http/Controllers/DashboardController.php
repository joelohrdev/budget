<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use Inertia\Response;

class DashboardController extends Controller
{
    public function index(): Response
    {
        $user = auth()->user();
        $activePayPeriod = $user->payPeriods()
            ->where('is_active', true)
            ->with(['cards.transactions' => function ($query) {
                $query->orderBy('transaction_date', 'desc');
            }])
            ->first();

        $dashboardData = null;
        if ($activePayPeriod) {
            $dashboardData = [
                'start_date' => $activePayPeriod->start_date->format('Y-m-d'),
                'end_date' => $activePayPeriod->end_date->format('Y-m-d'),
                'cards' => $activePayPeriod->cards->map(function ($card) {
                    return [
                        'id' => $card->id,
                        'name' => $card->name,
                        'type' => $card->type,
                        'budget_limit' => (float) $card->budget_limit,
                        'total_spent' => $card->totalSpent(),
                        'total_credits' => $card->totalCredits(),
                        'remaining_budget' => $card->remainingBudget(),
                    ];
                }),
            ];
        }

        $categories = $user->categories()->orderBy('name')->get()->map(function ($category) {
            return [
                'id' => $category->id,
                'name' => $category->name,
                'color' => $category->color,
            ];
        });

        return Inertia::render('dashboard', [
            'activePayPeriod' => $dashboardData,
            'categories' => $categories,
        ]);
    }
}
