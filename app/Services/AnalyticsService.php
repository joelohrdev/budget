<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\DB;

class AnalyticsService
{
    public function __construct(protected User $user) {}

    /**
     * Get the date format SQL for extracting year-month based on database driver
     */
    protected function getMonthFormatSql(string $column): string
    {
        $driver = DB::connection()->getDriverName();

        return match ($driver) {
            'mysql' => "DATE_FORMAT({$column}, '%Y-%m')",
            'pgsql' => "TO_CHAR({$column}, 'YYYY-MM')",
            'sqlite' => "strftime('%Y-%m', {$column})",
            default => "DATE_FORMAT({$column}, '%Y-%m')",
        };
    }

    /**
     * Get category spending breakdown for a date range
     */
    public function getCategorySpending(?string $startDate = null, ?string $endDate = null): array
    {
        $query = DB::table('transactions')
            ->join('cards', 'transactions.card_id', '=', 'cards.id')
            ->join('categories', 'transactions.category_id', '=', 'categories.id')
            ->where('cards.user_id', $this->user->id)
            ->where('transactions.type', 'debit')
            ->select(
                'categories.id',
                'categories.name',
                'categories.color',
                DB::raw('SUM(transactions.amount) as total'),
                DB::raw('COUNT(transactions.id) as transaction_count'),
                DB::raw('AVG(transactions.amount) as average')
            )
            ->groupBy('categories.id', 'categories.name', 'categories.color');

        if ($startDate && $endDate) {
            $query->whereBetween('transactions.transaction_date', [$startDate, $endDate]);
        }

        return $query->orderByDesc('total')
            ->get()
            ->map(function ($row) {
                return [
                    'id' => $row->id,
                    'name' => $row->name,
                    'color' => $row->color,
                    'total' => (float) $row->total,
                    'transaction_count' => $row->transaction_count,
                    'average' => (float) $row->average,
                ];
            })
            ->toArray();
    }

    /**
     * Get spending trends over time (monthly aggregation)
     */
    public function getSpendingTrends(int $months = 6): array
    {
        $monthFormat = $this->getMonthFormatSql('transaction_date');

        $results = DB::table('transactions')
            ->join('cards', 'transactions.card_id', '=', 'cards.id')
            ->leftJoin('categories', 'transactions.category_id', '=', 'categories.id')
            ->where('cards.user_id', $this->user->id)
            ->where('transactions.type', 'debit')
            ->where('transactions.transaction_date', '>=', now()->subMonths($months)->startOfMonth())
            ->select(
                DB::raw("{$monthFormat} as month"),
                'categories.name as category_name',
                DB::raw('SUM(transactions.amount) as total')
            )
            ->groupBy('month', 'category_name')
            ->orderBy('month')
            ->get();

        $monthlyData = [];
        foreach ($results as $row) {
            if (! isset($monthlyData[$row->month])) {
                $monthlyData[$row->month] = [
                    'month' => $row->month,
                    'total' => 0,
                    'categories' => [],
                ];
            }

            $amount = (float) $row->total;
            $monthlyData[$row->month]['total'] += $amount;

            if ($row->category_name) {
                $monthlyData[$row->month]['categories'][$row->category_name] = $amount;
            }
        }

        return array_values($monthlyData);
    }

    /**
     * Get budget vs actual comparison for the current pay period
     */
    public function getBudgetVsActual(): array
    {
        $activePayPeriod = $this->user->payPeriods()
            ->where('is_active', true)
            ->first();

        if (! $activePayPeriod) {
            return [];
        }

        return $activePayPeriod->cards()
            ->with('transactions')
            ->get()
            ->map(function ($card) {
                $spent = $card->totalSpent();
                $budgetLimit = (float) $card->budget_limit;
                $percentUsed = $budgetLimit > 0 ? ($spent / $budgetLimit) * 100 : 0;

                return [
                    'name' => $card->name,
                    'type' => $card->type,
                    'budget_limit' => $budgetLimit,
                    'spent' => $spent,
                    'remaining' => $card->remainingBudget(),
                    'percent_used' => round($percentUsed, 2),
                    'over_budget' => $spent > $budgetLimit,
                ];
            })
            ->toArray();
    }

    /**
     * Get top spending categories for a date range
     */
    public function getTopCategories(int $limit = 5, ?string $startDate = null, ?string $endDate = null): array
    {
        $categories = $this->getCategorySpending($startDate, $endDate);

        return array_slice($categories, 0, $limit);
    }

    /**
     * Get month-over-month spending comparison
     */
    public function getMonthOverMonthComparison(): array
    {
        $currentMonth = now()->format('Y-m');
        $previousMonth = now()->subMonth()->format('Y-m');
        $monthFormat = $this->getMonthFormatSql('transaction_date');

        $currentMonthSpending = DB::table('transactions')
            ->join('cards', 'transactions.card_id', '=', 'cards.id')
            ->where('cards.user_id', $this->user->id)
            ->where('transactions.type', 'debit')
            ->where(DB::raw($monthFormat), $currentMonth)
            ->sum('transactions.amount');

        $previousMonthSpending = DB::table('transactions')
            ->join('cards', 'transactions.card_id', '=', 'cards.id')
            ->where('cards.user_id', $this->user->id)
            ->where('transactions.type', 'debit')
            ->where(DB::raw($monthFormat), $previousMonth)
            ->sum('transactions.amount');

        $difference = (float) $currentMonthSpending - (float) $previousMonthSpending;
        $percentChange = $previousMonthSpending > 0
            ? ($difference / (float) $previousMonthSpending) * 100
            : 0;

        return [
            'current_month' => [
                'month' => $currentMonth,
                'total' => (float) $currentMonthSpending,
            ],
            'previous_month' => [
                'month' => $previousMonth,
                'total' => (float) $previousMonthSpending,
            ],
            'difference' => $difference,
            'percent_change' => round($percentChange, 2),
        ];
    }

    /**
     * Get overall spending summary
     */
    public function getSpendingSummary(?string $startDate = null, ?string $endDate = null): array
    {
        $query = DB::table('transactions')
            ->join('cards', 'transactions.card_id', '=', 'cards.id')
            ->where('cards.user_id', $this->user->id);

        if ($startDate && $endDate) {
            $query->whereBetween('transactions.transaction_date', [$startDate, $endDate]);
        }

        $debits = (float) (clone $query)->where('transactions.type', 'debit')->sum('transactions.amount');
        $credits = (float) (clone $query)->where('transactions.type', 'credit')->sum('transactions.amount');

        $transactionCount = (clone $query)->count();
        $avgTransaction = $transactionCount > 0 ? $debits / $transactionCount : 0;

        return [
            'total_spent' => $debits,
            'total_credits' => $credits,
            'net_spending' => $debits - $credits,
            'transaction_count' => $transactionCount,
            'average_transaction' => round($avgTransaction, 2),
        ];
    }
}
