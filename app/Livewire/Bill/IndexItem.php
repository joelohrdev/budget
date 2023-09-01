<?php

namespace App\Livewire\Bill;

use App\Models\Bill;
use Livewire\Component;

class IndexItem extends Component
{
    public Bill $bill;
    public $balance;
    public $percentage;

    public function mount()
    {
        $this->percentage = ($this->bill->balance / $this->bill->limit * 100);
        $this->balance = $this->bill->balance - $this->bill->payments->sum('amount');
    }

    public function render()
    {
        return view('livewire.bill.index-item');
    }
}
