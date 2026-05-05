<x-filament-panels::page>
    <form wire:submit="send">
        {{ $this->form }}

        <div class="mt-6 flex justify-end">
            <x-filament::button type="submit" icon="heroicon-o-paper-airplane">
                {{ __('Send Notification') }}
            </x-filament::button>
        </div>
    </form>
</x-filament-panels::page>
