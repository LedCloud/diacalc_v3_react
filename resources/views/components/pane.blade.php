@props([
    'active',
    'header',
    'prefix'
    ])

@php
    $prefix = $prefix ?? 'menu';
    $classes = "tab-pane panes__pane $prefix-panes__pane" . (($active ?? false) ? ' active' : '');
@endphp

<div {{ $attributes->merge(['class' => $classes]) }}>
    <div class="{{ $prefix }}-panes__pane_header panes__pane_header">{{ $header }}</div>

    <div class="{{ $prefix }}-panes__pane_content panes__pane_content">
        {{ $slot }}
    </div>
</div>
