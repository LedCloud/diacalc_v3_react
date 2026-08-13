@props([
    'active',
    'section'
])
<button type="button"
        wire:click="$set('section', '{{ $section }}')"
        class="tab-btn {{ $active ? 'active' : '' }}"
>{{ $slot }}
</button>
