<?php

namespace App\Livewire\Bill;

use App\Models\Bill;
use Livewire\Component;

class TotalDebt extends Component
{
    public $total;

    public function mount()
    {
        $this->total = Bill::sum('balance');
    }
    public function render()
    {
        return <<<'HTML'
        <div>
           ${{ number_format($total, 2) }}
        </div>
        HTML;
    }
}
