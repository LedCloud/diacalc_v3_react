<x-app-layout>
{{--    <x-slot name="header">--}}
{{--        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">--}}
{{--            {{ __('Dashboard') }}--}}
{{--        </h2>--}}
{{--    </x-slot>--}}

        <div class="grid grid-cols-[2fr_5fr] gap-1 justify-center">
            <x-menu />
            <livewire:menu.toolbar />
        </div>
    <x-toast-msg />
</x-app-layout>
