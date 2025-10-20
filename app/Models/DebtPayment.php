<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DebtPayment extends Model
{
    /** @use HasFactory<\Database\Factories\DebtPaymentFactory> */
    use HasFactory;

    protected $fillable = [
        'debt_id',
        'amount',
        'principal_amount',
        'interest_amount',
        'payment_date',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'amount' => 'decimal:2',
            'principal_amount' => 'decimal:2',
            'interest_amount' => 'decimal:2',
            'payment_date' => 'date',
        ];
    }

    public function debt(): BelongsTo
    {
        return $this->belongsTo(Debt::class);
    }
}
