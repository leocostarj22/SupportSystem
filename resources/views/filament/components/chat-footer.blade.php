<div class="fixed bottom-0 left-0 right-0 bg-white border-t p-4 shadow-lg">
    <form wire:submit="create" class="flex gap-2 items-end max-w-4xl mx-auto">
        <div class="flex-1">
            {{ $this->form }}
        </div>
        <x-filament::button type="submit" color="primary">
            <x-filament::icon icon="heroicon-m-paper-airplane" class="w-5 h-5" />
        </x-filament::button>
    </form>
</div>