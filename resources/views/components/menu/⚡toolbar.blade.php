<?php

use Livewire\Component;

new class extends Component
{
    public $groups;

    public function mount()
    {
        $this->groups = auth()->user()->productGroups()->orderBy('sort_order', 'asc')->get();
    }
};
?>

<div class="border p-2 border-amber-500">
    <div class="products-pane">
    <ul>
   @foreach($groups as $group)
       <li>{{ $group->name }} {{ $group->sort_order }} </li>
   @endforeach
    </ul>
    </div>
</div>
