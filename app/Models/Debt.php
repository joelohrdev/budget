<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Debt extends Model
{
    /** @use HasFactory<\Database\Factories\DebtFactory> */
    use HasFactory;

    protected $fillable = [
        'user_id',
        'name',
        'type',
        'principal_amount',
        'current_balance',
        'interest_rate',
        'minimum_payment',
        'term_months',
        'start_date',
        'payoff_target_date',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'principal_amount' => 'decimal:2',
            'current_balance' => 'decimal:2',
            'interest_rate' => 'decimal:2',
            'minimum_payment' => 'decimal:2',
            'start_date' => 'date',
            'payoff_target_date' => 'date',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(DebtPayment::class);
    }

    public function calculateMonthlyInterest(): float
    {
        return round($this->current_balance * ($this->interest_rate / 100 / 12), 2);
    }

    public function calculatePayoffMonths(float $monthlyPayment): ?int
    {
        if ($monthlyPayment <= $this->calculateMonthlyInterest()) {
            return null; // Payment doesn't cover interest
        }

        $balance = (float) $this->current_balance;
        $monthlyRate = $this->interest_rate / 100 / 12;
        $months = 0;

        while ($balance > 0 && $months < 600) { // Cap at 50 years
            $interestCharge = $balance * $monthlyRate;
            $principalPayment = min($monthlyPayment - $interestCharge, $balance);
            $balance -= $principalPayment;
            $months++;
        }

        return $months;
    }

    public function generatePayoffSchedule(float $monthlyPayment): array
    {
        $schedule = [];
        $balance = (float) $this->current_balance;
        $monthlyRate = $this->interest_rate / 100 / 12;
        $currentDate = now();

        while ($balance > 0.01 && count($schedule) < 600) {
            $interestCharge = round($balance * $monthlyRate, 2);
            $principalPayment = round(min($monthlyPayment - $interestCharge, $balance), 2);
            $balance = round($balance - $principalPayment, 2);

            $schedule[] = [
                'payment_number' => count($schedule) + 1,
                'date' => $currentDate->copy()->addMonths(count($schedule))->format('Y-m-d'),
                'payment_amount' => round($interestCharge + $principalPayment, 2),
                'principal' => $principalPayment,
                'interest' => $interestCharge,
                'balance' => max($balance, 0),
            ];

            if ($principalPayment <= 0) {
                break;
            }
        }

        return $schedule;
    }
}
