<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Card extends Model
{
    /** @use HasFactory<\Database\Factories\CardFactory> */
    use HasFactory;

    protected $fillable = [
        'user_id',
        'pay_period_id',
        'name',
        'type',
        'budget_limit',
    ];

    protected function casts(): array
    {
        return [
            'budget_limit' => 'decimal:2',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function payPeriod(): BelongsTo
    {
        return $this->belongsTo(PayPeriod::class);
    }

    public function transactions(): HasMany
    {
        return $this->hasMany(Transaction::class);
    }

    public function totalSpent(): float
    {
        return (float) $this->transactions()
            ->where('type', 'debit')
            ->sum('amount');
    }

    public function totalCredits(): float
    {
        return (float) $this->transactions()
            ->where('type', 'credit')
            ->sum('amount');
    }

    public function remainingBudget(): float
    {
        return (float) $this->budget_limit - $this->totalSpent() + $this->totalCredits();
    }
}
