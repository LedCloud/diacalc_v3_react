@props([
    'type' => 'notice',
])

@php
    $class = match ($type) {
        'warning' => 'notice__warning',
        'error' => 'notice__error',
        default=> 'notice__notice',
    }
@endphp

<div class="notice {{ $class }}">{{ $slot }}</div>
