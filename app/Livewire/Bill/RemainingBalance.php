<?php

namespace App\Livewire\Bill;

use App\Models\Bill;
use Livewire\Component;

class RemainingBalance extends Component
{
    public Bill $bill;
    public float $remainingBalance;

    public function mount(): void
    {
        $this->remainingBalance = $this->bill->balance - $this->bill->payments->sum('amount');
    }

    public function render()
    {
        return <<<'HTML'
        <div>
            ${{ $remainingBalance }}
        </div>
        HTML;
    }
}
