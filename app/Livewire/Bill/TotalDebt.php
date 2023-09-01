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
           ${{ $total }}
        </div>
        HTML;
    }
}
