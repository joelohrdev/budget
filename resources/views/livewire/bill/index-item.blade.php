<tr>
    <td class="whitespace-nowrap py-2 pl-4 pr-3 text-sm text-gray-500 sm:pl-0">{{ $bill->name }}</td>
    <td class="whitespace-nowrap px-2 py-2 text-sm text-gray-500">{{ $bill->due_date->format('M d, Y') }}</td>
    <td class="whitespace-nowrap px-2 py-2 text-sm text-gray-500">${{ number_format($bill->balance, 2) }}</td>
    <td class="whitespace-nowrap px-2 py-2 text-sm text-gray-500">${{ number_format($bill->limit, 2) }}</td>
    <td class="whitespace-nowrap px-2 py-2 text-sm text-gray-500">{{ $bill->rate }}%</td>
    <td class="whitespace-nowrap px-2 py-2 text-sm text-gray-500">
        <div class="flex h-2 overflow-hidden rounded bg-green-600 w-[50px]">
            <div style="transform: scale({{ $percentage / 100 }}, 1)"
                 class="bg-red-500 transition-transform origin-left duration-200 ease-in-out w-full shadow-none flex flex-col"></div>
        </div>
    </td>
    <td class="relative whitespace-nowrap py-2 pl-3 pr-4 text-right text-sm font-medium sm:pr-0">
        <a wire:navigate href="{{ route('bill.show', $bill) }}" class="text-emerald-600 hover:text-emerald-900">View<span class="sr-only">, {{ $bill->name }}</span></a>
    </td>
</tr>
