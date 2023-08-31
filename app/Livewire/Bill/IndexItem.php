<?php

namespace App\Livewire\Bill;

use App\Models\Bill;
use Livewire\Component;

class IndexItem extends Component
{
    public Bill $bill;
    public function render()
    {
        return view('livewire.bill.index-item');
    }
}
