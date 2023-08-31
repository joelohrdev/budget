<?php

namespace App\Livewire\Bill;

use App\Models\Bill;
use Illuminate\Validation\Rule;
use Livewire\Component;

class Create extends Component
{
    #[Rule('required|min:3|max:255')]
    public $name;
    #[Rule('required|date')]
    public $dueDate;
    #[Rule('required|float')]
    public $balance;
    #[Rule('required|float')]
    public $limit;
    #[Rule('required|float')]
    public $rate;
    #[Rule('required')]
    public $type;

    public function create()
    {
        Bill::create([
            'name' => $this->name,
            'due_date' => $this->dueDate,
            'balance' => $this->balance,
            'limit' => $this->limit,
            'rate' => $this->rate,
            'type' => $this->type,
        ]);

        session()->flash('message', 'Bill successfully created.');

        return redirect()->route('bill.index');
    }
    public function render()
    {
        return view('livewire.bill.create');
    }
}
