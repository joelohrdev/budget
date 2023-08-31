<?php

namespace App\Livewire\Payment;

use App\Models\Payment;
use Livewire\Component;

class IndexItem extends Component
{
    public Payment $payment;
    public function render()
    {
        return view('livewire.payment.index-item');
    }
}
