
<div>
    <form class="factors__form" wire:submit="save">
    <div class="coefs-panes">
        <x-pane :header="__('coefs.coefficients')" prefix="coefs">
            <div class="horizontal">
                <div class="field">
                    <label for="factors_by_time">
                        <input type="checkbox"
                               id="factors_by_time"
                               wire:model="factors_by_time" />
                        {{ __('coefs.calculate_factors_by_time') }}
                    </label>
                </div>
                <a class="btn" href="{{ route('factors.create') }}"
                >+</a>
            </div>
            <table class="table table-responsive">
                <thead>
                    <tr>
                        <th>{{ __('coefs.time') }}</th>
                        <th>{{ __('coefs.k1') }}</th>
                        <th>{{ __('coefs.k2') }}</th>
                        <th>{{ __('coefs.k3') }}</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($factors as $factor)
                        <tr wire:key="factor-{{ $factor->id }}">
                            <td>
                                {{ $factor->time->format('H:i') }}
                            </td>
                            <td>
                                {{ $factor->k1formatted }}
                            </td>
                            <td>
                                {{ $factor->k2formatted }}
                            </td>
                            <td>
                                {{ $factor->k3formatted }}
                            </td>
                            <td >
                                <x-dropdown contentClasses="flex flex-col py-1 bg-white dark:bg-gray-700">
                                    <x-slot name="trigger">
                                        <button class="text-gray-400 hover:text-gray-600 transition">
                                            <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 20 20">
                                                <path d="M10 6a2 2 0 110-4 2 2 0 010 4zM10 12a2 2 0 110-4 2 2 0 010 4zM10 18a2 2 0 110-4 2 2 0 010 4z" />
                                            </svg>
                                       </button>
                                    </x-slot>
                                    <x-slot name="content" >
                                        <a type="button"
                                           href="{{ route('factors.edit', ['id' => $factor->id]) }}"
                                           class="hover:bg-slate-100"
                                        >
                                            {{ __('Edit') }}
                                        </a>
                                        <button type="button"
                                                class="hover:bg-slate-100"
                                                wire:key="delete-factor-{{ $factor->id }}"
                                                x-on:click.prevent="@this.call('deleteFactor', {{ $factor->id }})"
                                        >
                                            {{ __('Delete') }}
                                        </button>
                                    </x-slot>
                                </x-dropdown>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </x-pane>
        <x-pane :header="__('coefs.coefficients')" prefix="coefs">
            <fieldset>
                <div class="field">
                    <label for="weight">{{ __('coefs.weight') }}</label>
                    <input type="number"
                           key="weight-field-{{ $weight }}"
                           min="1" step="1"
                           value="{{ $weight }}"
                           wire:model="weight" />
                    <div class="validation-error">@error('weight') {{ $message }} @enderror</div>
                </div>

                <div class="field">
                    <label for="k3_factor">{{ __('coefs.k3_factor') }}</label>
                    <input type="number"
                           min="1" step="1"
                           wire:model="k3_factor" />
                    <div class="validation-error">@error('k3_factor') {{ $message }} @enderror</div>
                </div>
                <button class="btn"
                        type="button"
                        wire:click="calculateFactors"
                >{{ __('coefs.calc_factors') }}</button>
                <x-panes.notice>{{ __('coefs.calculate_explain') }}</x-panes.notice>
            </fieldset>
        </x-pane>
    </div>
        <div class="button-horizontal">
            <button class="btn settings__btn-save primary" type="submit">{{ __('inputs.save') }}</button>
            <a class="btn settings__btn-calcel default" href="{{ route("dashboard") }}">{{ __('inputs.cancel') }}</a>
        </div>
    </form>

    <x-toast-msg />

</div>
