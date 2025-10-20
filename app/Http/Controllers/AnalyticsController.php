<?php

namespace App\Http\Controllers;

use App\Services\AnalyticsService;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class AnalyticsController extends Controller
{
    public function index(Request $request): Response
    {
        $user = auth()->user();
        $analytics = new AnalyticsService($user);

        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');

        return Inertia::render('analytics', [
            'summary' => $analytics->getSpendingSummary($startDate, $endDate),
            'categorySpending' => $analytics->getCategorySpending($startDate, $endDate),
            'topCategories' => $analytics->getTopCategories(5, $startDate, $endDate),
            'budgetVsActual' => $analytics->getBudgetVsActual(),
            'spendingTrends' => $analytics->getSpendingTrends(6),
            'monthOverMonth' => $analytics->getMonthOverMonthComparison(),
            'filters' => [
                'start_date' => $startDate,
                'end_date' => $endDate,
            ],
        ]);
    }
}
