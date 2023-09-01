<?php

namespace App\Livewire\Bill;

use App\Models\Payment;
use Livewire\Component;

class PaymentHistory extends Component
{
    public $payments;

    public function mount()
    {
        $this->payments = Payment::query()
            ->orderBy('date', 'desc')
            ->with('bill')
            ->latest()
            ->take(5)
            ->get();
    }
    public function render()
    {
        return <<<'HTML'
        <ul role="list" class="activity mt-6 space-y-6">
        @foreach ($payments as $payment)
            <li class="relative flex gap-x-4">
                <div class="absolute left-0 top-0 flex w-6 justify-center -bottom-6">
                    <div class="bar w-px bg-gray-200"></div>
                </div>
                <div class="relative flex h-6 w-6 flex-none items-center justify-center bg-white">
                    <div class="h-1.5 w-1.5 rounded-full bg-gray-100 ring-1 ring-gray-300"></div>
                </div>
                <p class="flex-auto py-0.5 text-xs leading-5 text-gray-500"><span class="font-medium text-gray-900">{{ $payment->bill->name }}</span> payment of ${{ $payment->amount}}</p>
                <time class="flex-none py-0.5 text-xs leading-5 text-gray-500">{{ \Carbon\Carbon::parse($payment->date)->diffForHumans() }}</time>
            </li>
        @endforeach
        </ul>
        HTML;
    }
}
