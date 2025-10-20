<?php

namespace App\Http\Controllers;

use App\Models\Debt;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class DebtCalculatorController extends Controller
{
    public function index(): Response
    {
        $user = auth()->user();

        $debts = $user->debts()
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function ($debt) {
                return [
                    'id' => $debt->id,
                    'name' => $debt->name,
                    'type' => $debt->type,
                    'current_balance' => (float) $debt->current_balance,
                    'interest_rate' => (float) $debt->interest_rate,
                    'minimum_payment' => $debt->minimum_payment ? (float) $debt->minimum_payment : null,
                    'monthly_interest' => $debt->calculateMonthlyInterest(),
                ];
            });

        return Inertia::render('debts/calculator', [
            'debts' => $debts,
        ]);
    }

    public function payoffSchedule(Request $request, Debt $debt): JsonResponse
    {
        if ($debt->user_id !== auth()->id()) {
            abort(403);
        }

        $request->validate([
            'monthly_payment' => ['required', 'numeric', 'min:0.01'],
        ]);

        $monthlyPayment = $request->input('monthly_payment');
        $schedule = $debt->generatePayoffSchedule($monthlyPayment);
        $months = $debt->calculatePayoffMonths($monthlyPayment);

        return response()->json([
            'schedule' => $schedule,
            'months' => $months,
            'total_interest' => array_sum(array_column($schedule, 'interest')),
            'total_paid' => $monthlyPayment * count($schedule),
        ]);
    }

    public function snowballAnalysis(Request $request): JsonResponse
    {
        $user = auth()->user();

        $request->validate([
            'extra_payment' => ['required', 'numeric', 'min:0'],
        ]);

        $extraPayment = $request->input('extra_payment');

        $debts = $user->debts()
            ->get()
            ->sortBy('current_balance')
            ->values();

        return response()->json($this->calculatePayoffStrategy($debts, $extraPayment, 'snowball'));
    }

    public function avalancheAnalysis(Request $request): JsonResponse
    {
        $user = auth()->user();

        $request->validate([
            'extra_payment' => ['required', 'numeric', 'min:0'],
        ]);

        $extraPayment = $request->input('extra_payment');

        $debts = $user->debts()
            ->get()
            ->sortByDesc('interest_rate')
            ->values();

        return response()->json($this->calculatePayoffStrategy($debts, $extraPayment, 'avalanche'));
    }

    private function calculatePayoffStrategy($debts, float $extraPayment, string $strategy): array
    {
        $timeline = [];
        $totalMonths = 0;
        $totalInterest = 0;
        $remainingDebts = $debts->map(function ($debt) {
            return [
                'id' => $debt->id,
                'name' => $debt->name,
                'balance' => (float) $debt->current_balance,
                'rate' => (float) $debt->interest_rate,
                'minimum' => $debt->minimum_payment ? (float) $debt->minimum_payment : 0,
            ];
        })->toArray();

        $month = 0;
        while (! empty($remainingDebts) && $month < 600) {
            $month++;
            $monthData = [
                'month' => $month,
                'payments' => [],
            ];

            $totalMinimums = array_sum(array_column($remainingDebts, 'minimum'));
            $availableExtra = $extraPayment;

            foreach ($remainingDebts as $index => &$debt) {
                $monthlyRate = $debt['rate'] / 100 / 12;
                $interest = round($debt['balance'] * $monthlyRate, 2);
                $totalInterest += $interest;

                $payment = $debt['minimum'];
                if ($index === 0) {
                    $payment += $availableExtra;
                }

                $principal = round(min($payment - $interest, $debt['balance']), 2);
                $debt['balance'] = round($debt['balance'] - $principal, 2);

                $monthData['payments'][] = [
                    'debt_id' => $debt['id'],
                    'debt_name' => $debt['name'],
                    'payment' => round($payment, 2),
                    'principal' => $principal,
                    'interest' => $interest,
                    'remaining_balance' => $debt['balance'],
                ];

                if ($debt['balance'] <= 0) {
                    unset($remainingDebts[$index]);
                    $remainingDebts = array_values($remainingDebts);
                }
            }

            $timeline[] = $monthData;
        }

        return [
            'strategy' => $strategy,
            'total_months' => $month,
            'total_interest' => round($totalInterest, 2),
            'timeline' => array_slice($timeline, 0, 12),
        ];
    }
}
