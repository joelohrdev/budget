<div>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ $bill->name }}
        </h2>
    </x-slot>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="grid gap-4 md:grid-cols-2 lg:grid-cols-8">
                <div class="rounded-xl border bg-white p-6 col-span-5">
                    <livewire:bill.update :bill="$bill" />
                </div>
                <div class="rounded-xl border bg-white col-span-3">
                    <dl class="flex flex-wrap">
                        <div class="pl-6 pt-6">
                            <dt class="text-sm font-semibold leading-6 text-gray-900">Remaining Balance</dt>
                            <dd class="mt-1 text-base font-semibold leading-6 text-gray-900">
                                <livewire:bill.remaining-balance :bill="$bill" />
                            </dd>
                        </div>
                    </dl>
                    <div class="p-6">
                        <div class="flex h-2 overflow-hidden rounded bg-green-600">
                            <div style="transform: scale({{ $percentage / 100 }}, 1)"
                                 class="bg-red-500 transition-transform origin-left duration-200 ease-in-out w-full shadow-none flex flex-col"></div>
                        </div>
                    </div>
                </div>
            </div>
            <livewire:payment.index :bill="$bill" :key="$bill->id" />
        </div>
    </div>
</div>
