<tr>
    <td class="whitespace-nowrap py-2 pl-4 pr-3 text-sm text-gray-500 sm:pl-0">${{ $payment->amount }}</td>
    <td class="whitespace-nowrap px-2 py-2 text-sm text-gray-500">{{ $payment->date->format('F d, Y') }}</td>
    <td class="relative whitespace-nowrap py-2 pl-3 pr-4 text-right text-sm font-medium sm:pr-0">
{{--        <a wire:navigate href="{{ route('bill.show', $bill) }}" class="text-indigo-600 hover:text-indigo-900">View<span class="sr-only">, {{ $bill->name }}</span></a>--}}
    </td>
</tr>
