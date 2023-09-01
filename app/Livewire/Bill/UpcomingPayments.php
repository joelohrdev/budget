<?php

namespace App\Livewire\Bill;

use App\Models\Bill;
use Carbon\Carbon;
use Livewire\Component;

class UpcomingPayments extends Component
{
    public $bills;

    public function mount()
    {
        $this->bills = Bill::query()
            ->where('due_date', '>=', Carbon::now())
            ->where('due_date', '<=', Carbon::now()->addDays(14))
            ->orderBy('due_date')
            ->get();
    }
    public function render()
    {
        return view('livewire.bill.upcoming-payments');
    }
}
