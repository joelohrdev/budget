<div>
    <ul role="list" class="divide-y divide-gray-100 mt-6">
        @forelse($bills as $bill)
            <li class="flex gap-x-4 py-3">
                <div class="w-full flex justify-between items-center">
                    <p class="text-sm font-semibold leading-6 text-gray-900">{{ $bill->name }}</p>
                    <p class="mt-1 truncate text-xs leading-5 text-gray-500">{{ $bill->due_date->format('F d, Y') }}</p>
                </div>
            </li>
        @empty
            <li class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 text-center">
                No upcoming payments.
            </li>
        @endforelse
    </ul>
</div>
