<div>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Bills') }}
            </h2>
            <div>
                <a
                    wire:navigate
                    class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:bg-gray-700 active:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150 mt-4"
                    href="{{ route('bill.create') }}">Create Bill</a>
            </div>
        </div>
    </x-slot>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 grid grid-cols-1 lg:grid-cols-3 gap-5">
            <div class="p-10 bg-white overflow-hidden rounded-lg shadow-sm ring-1 ring-gray-900/5 col-span-2">
                <div class="flow-root">
                    <div class="-mx-4 -my-2 overflow-x-auto sm:-mx-6 lg:-mx-8">
                        <div class="inline-block min-w-full py-2 align-middle sm:px-6 lg:px-8">
                            <table class="min-w-full divide-y divide-gray-300">
                                <thead>
                                <tr>
                                    <th scope="col" class="whitespace-nowrap py-3.5 pl-4 pr-3 text-left text-sm font-semibold text-gray-900 sm:pl-0">Name</th>
                                    <th scope="col" class="whitespace-nowrap px-2 py-3.5 text-left text-sm font-semibold text-gray-900">Due Date</th>
                                    <th scope="col" class="whitespace-nowrap px-2 py-3.5 text-left text-sm font-semibold text-gray-900">Balance</th>
                                    <th scope="col" class="whitespace-nowrap px-2 py-3.5 text-left text-sm font-semibold text-gray-900">Limit</th>
                                    <th scope="col" class="whitespace-nowrap px-2 py-3.5 text-left text-sm font-semibold text-gray-900">Interest Rate</th>
                                    <th scope="col" class="whitespace-nowrap px-2 py-3.5 text-left text-sm font-semibold text-gray-900">Type</th>
                                    <th scope="col" class="relative whitespace-nowrap py-3.5 pl-3 pr-4 sm:pr-0">
                                        <span class="sr-only">View</span>
                                    </th>
                                </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-200 bg-white">
                                @forelse ($bills as $bill)
                                    <livewire:bill.index-item :bill="$bill" :key="$bill->id" />
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
                    </div>
                </div>
            </div>
            <div class="rounded-lg bg-white shadow-sm ring-1 ring-gray-900/5">
                <dl class="flex flex-wrap">
                    <div class="flex-auto pl-6 pt-6">
                        <dt class="text-sm font-semibold leading-6 text-gray-900">Total Debt</dt>
                        <dd class="mt-1 text-base font-semibold leading-6 text-gray-900"><livewire:bill.total-debt /></dd>
                    </div>
{{--                    <div class="flex-none self-end px-6 pt-4">--}}
{{--                        <dt class="sr-only">Status</dt>--}}
{{--                        <dd class="rounded-md bg-green-50 px-2 py-1 text-xs font-medium text-green-600 ring-1 ring-inset ring-green-600/20">Paid</dd>--}}
{{--                    </div>--}}

                    <div class="mt-4 flex w-full flex-none gap-x-4 px-6 py-6 border-t border-gray-900/5">
                        <div class="w-full">
                            <h2 class="text-sm font-semibold leading-6 text-gray-900">Upcoming Payments</h2>
                            <livewire:bill.upcoming-payments />
                        </div>
                    </div>

                    <div class="mt-4 flex w-full flex-none gap-x-4 px-6 py-6 border-t border-gray-900/5">
                        <div class="w-full">
                            <!-- Activity feed -->
                            <h2 class="text-sm font-semibold leading-6 text-gray-900">Activity</h2>
                            <livewire:bill.payment-history />
                        </div>
                    </div>

                </dl>
            </div>
        </div>
    </div>
</div>
