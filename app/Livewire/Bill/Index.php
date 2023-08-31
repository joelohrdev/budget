<?php

namespace App\Livewire\Bill;

use App\Models\Bill;
use Livewire\Component;

class Index extends Component
{
    public function render()
    {
        return view('livewire.bill.index', [
            'bills' => Bill::latest()->get(),
        ]);
    }
}
