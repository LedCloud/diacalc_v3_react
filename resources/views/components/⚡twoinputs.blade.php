<?php

use Livewire\Component;
use Livewire\Attributes\Reactive;
use Livewire\Attributes\Computed;
use Livewire\Attributes\On;

new class extends Component
{
    public int $av = 2;
    public int $bv = 4;
    public bool $selected = true;

    //as a constructor
    public function mount()
    {
        $this->av = 2;
        $this->bv = 4;
        $this->selected = true;
    }

    #[On('refresh-component')]
    public function refresh() {}

    public function boot()
    {
        //$this->bv = 2 * $this->av;
    }
    public function render()
    {
        $this->bv = 2 * $this->av;
        return $this->view([
            'bv' => $this->av * 2,
        ]);
    }

    public function updatedSelected($value)
    {
        $this->selected = (bool)$value;
        if ($this->selected) {
            $this->av = 2;
        } else {
            $this->av = 5;
        }
        $this->bv = $this->av * 2;
        ////$this->dispatch('refresh-component');
        $this->dispatch('force-update-av', value: $this->av);

        $this->dispatch('force-update-multiple',
        av: $this->av,
        bv: $this->bv,
        title: $this->title
    );
    }

    public function updatedBv($value)
    {
        $this->bv = $value ?? 0;
        $this->av = intval($this->bv / 2);
        //$this->dispatch('refresh-component');
    }

    public function updatedAv($value)
    {
        $this->av = $value ?? 0;
        $this->bv = $this->av * 2;
        //$this->dispatch('refresh-component');
    }

};
?>

<div>
    Two inputs
    <input type="checkbox" wire:model.live="selected" />
    <div wire:ignore.self>
    <input  id="av"
        wire:model="av"
        x-on:force-update-av.window="$el.value = $event.detail.value"
x-on:force-update-multiple.window="$el.value = $event.detail.av"
            />
    </div>

    <input type="text"
        wire:model.live.debounce.500ms="bv"
        wire:key="input-{{ $bv }}"
            />
    <br>
    <ul>
        <li>{{ $av }}</li>
        <li>{{ $bv }}</li>
    </ul>
    <span wire:loading>Saving...</span>
</div>
