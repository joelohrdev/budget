<form wire:submit.prevent="update">
    <div class="grid grid-cols-2 gap-5">
        <div>
            <x-label for="name" value="{{ __('Name') }}" />
            <x-input id="name" type="text" class="mt-1 block w-full shadow-none" wire:model="name" required autocomplete="name" />
            <x-input-error for="name" class="mt-2" />
        </div>
        <div>
            <x-label for="dueDate" value="{{ __('Due Date') }}" />
            <x-input id="dueDate" type="date" class="mt-1 block w-full shadow-none" wire:model="dueDate" required autocomplete="dueDate" />
            <x-input-error for="dueDate" class="mt-2" />
        </div>
    </div>
    <div class="grid grid-cols-4 gap-5 mt-5">
        <div>
            <x-label for="balance" value="{{ __('Balance') }}" />
            <x-input id="balance" type="text" class="mt-1 block w-full shadow-none" wire:model="balance" required autocomplete="balance" />
            <x-input-error for="balance" class="mt-2" />
        </div>
        <div>
            <x-label for="limit" value="{{ __('Limit') }}" />
            <x-input id="limit" type="text" class="mt-1 block w-full shadow-none" wire:model="limit" required autocomplete="limit" />
            <x-input-error for="limit" class="mt-2" />
        </div>
        <div>
            <x-label for="rate" value="{{ __('Interest Rate') }}" />
            <x-input id="rate" type="text" class="mt-1 block w-full shadow-none" wire:model="rate" required autocomplete="rate" />
            <x-input-error for="rate" class="mt-2" />
        </div>
        <div>
            <x-label for="type" value="{{ __('Type') }}" />
            <x-input id="type" type="text" class="mt-1 block w-full shadow-none" wire:model="type" required autocomplete="type" />
            <x-input-error for="type" class="mt-2" />
        </div>
    </div>
    <div class="mt-5">
        <x-button>Update</x-button>
        <a
            wire:navigate
            class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 disabled:opacity-25 transition ease-in-out duration-150"
            href="{{ route('bill.index') }}">Cancel</a>

    </div>
</form>
