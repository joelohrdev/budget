<?php

namespace App\Livewire\Payment;

use App\Models\Bill;
use Livewire\Component;

class Index extends Component
{
    public Bill $bill;

    public function render()
    {
        return view('livewire.payment.index', [
            'payments' => $this->bill->payments()->latest()->get(),
        ]);
    }
}
