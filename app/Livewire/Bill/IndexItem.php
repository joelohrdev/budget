<?php

namespace App\Livewire\Bill;

use App\Models\Bill;
use Livewire\Component;

class IndexItem extends Component
{
    public Bill $bill;
    public $percentage;

    public function mount()
    {
        $this->percentage = ($this->bill->balance / $this->bill->limit * 100);
    }

    public function render()
    {
        return view('livewire.bill.index-item');
    }
}
