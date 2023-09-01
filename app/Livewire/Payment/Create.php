<?php

namespace App\Livewire\Payment;

use App\Models\Bill;
use Illuminate\Validation\Rule;
use Livewire\Component;

class Create extends Component
{
    public Bill $bill;
    #[Rule('required', 'numeric', 'min:1')]
    public $amount;
    #[Rule('required', 'date')]
    public $date;

    public function create()
    {
        $this->bill->update([
            'balance' => $this->bill->balance - $this->amount,
        ]);

        $this->bill->payments()->create([
            'amount' => $this->amount,
            'date' => $this->date,
            'balance' => $this->bill->balance
        ]);

        session()->flash('message', 'Bill successfully created.');

        return redirect()->route('bill.show', $this->bill->slug);
    }

    public function render()
    {
        return view('livewire.payment.create');
    }
}
