<div class="menu-container">
    <x-menu.controls />
    <div class="menu-content">
        @for ($i = 0; $i < 3; $i++)
            <x-menu.item :tabindex="$i + 1">
                The current value is {{ $i }}
            </x-menu.item>
        @endfor
    </div>
</div>
