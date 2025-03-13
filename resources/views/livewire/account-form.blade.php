<div>
    <form wire:submit="submit">
        {{ $this->form }}

        <br>
        <x-filament::button type="submit">
            Submit
        </x-filament::button>

    </form>

    <x-filament-actions::modals />
</div>