<div>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Add Payment to {{ $bill->name }}
            </h2>
            <div>

            </div>
        </div>
    </x-slot>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="p-10 bg-white overflow-hidden shadow-xl sm:rounded-lg">
                <div class="flow-root">
                    <div class="-mx-4 -my-2 overflow-x-auto sm:-mx-6 lg:-mx-8">
                        <div class="inline-block min-w-full py-2 align-middle sm:px-6 lg:px-8">
                            <form wire:submit.prevent="create">
                                <div class="grid grid-cols-2 gap-5">
                                    <div>
                                        <x-label for="amount" value="{{ __('Payment Amount') }}" />
                                        <x-input id="amount" type="number" class="mt-1 block w-full shadow-none" wire:model="amount" required autocomplete="amount" />
                                        <x-input-error for="amount" class="mt-2" />
                                    </div>
                                    <div>
                                        <x-label for="date" value="{{ __('Payment Date') }}" />
                                        <x-input id="date" type="date" class="mt-1 block w-full shadow-none" wire:model="date" required autocomplete="date" />
                                        <x-input-error for="date" class="mt-2" />
                                    </div>
                                </div>
                                <div class="mt-5">
                                    <x-button>Create</x-button>
                                    <a
                                        wire:navigate
                                        class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 disabled:opacity-25 transition ease-in-out duration-150"
                                        href="{{ route('bill.show', $bill) }}">Cancel</a>

                                </div>
                            </form>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
