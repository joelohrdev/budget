<div class="rounded-xl border bg-white p-6 col-span-5 mt-5">
    <div class="flex justify-between items-center">
        <h2 class="text-2xl font-bold">Payments</h2>
        <div>
            <a
                wire:navigate
                class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:bg-gray-700 active:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150 mt-4"
                href="{{ route('payment.create', $bill) }}">Add Payment</a>
        </div>
    </div>
<table class="min-w-full divide-y divide-gray-300">
    <thead>
    <tr>
        <th scope="col" class="whitespace-nowrap py-3.5 pl-4 pr-3 text-left text-sm font-semibold text-gray-900 sm:pl-0">Amount</th>
        <th scope="col" class="whitespace-nowrap px-2 py-3.5 text-left text-sm font-semibold text-gray-900">Date</th>
        <th scope="col" class="relative whitespace-nowrap py-3.5 pl-3 pr-4 sm:pr-0">
            <span class="sr-only">View</span>
        </th>
    </tr>
    </thead>
    <tbody class="divide-y divide-gray-200 bg-white">
    @forelse ($payments as $payment)
        <livewire:payment.index-item :payment="$payment" />
    @empty
        <tr>
            <td colspan="8" class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 text-center">
                No bills found.
            </td>
        </tr>
    @endforelse
    </tbody>
</table>
</div>
