<?php

namespace App\Livewire\Bill;

use App\Models\Bill;
use Livewire\Component;

class RemainingBalance extends Component
{
    public Bill $bill;

    public function render()
    {
        return <<<'HTML'
        <div>
            ${{ $bill->balance }}
        </div>
        HTML;
    }
}
