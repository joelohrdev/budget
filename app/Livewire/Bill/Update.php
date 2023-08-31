<?php

namespace App\Livewire\Bill;

use App\Models\Bill;
use Livewire\Component;

class Update extends Component
{
    public Bill $bill;
    public $name;
    public $dueDate;
    public $balance;
    public $limit;
    public $rate;
    public $type;

    public function mount()
    {
        $this->name = $this->bill->name;
        $this->dueDate = $this->bill->due_date->format('Y-m-d');
        $this->balance = $this->bill->balance;
        $this->limit = $this->bill->limit;
        $this->rate = $this->bill->rate;
        $this->type = $this->bill->type;
    }

    public function update()
    {
        $this->bill->update([
            'name' => $this->name,
            'due_date' => $this->dueDate,
            'balance' => $this->balance,
            'limit' => $this->limit,
            'rate' => $this->rate,
            'type' => $this->type,
        ]);
    }

    public function render()
    {
        return view('livewire.bill.update');
    }
}
