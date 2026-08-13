
<div>
    <form wire:submit="save" class="mt-6 space-y-6">
        <div class="grid md:grid-cols-2 grid-cols-1 justify-center gap-3 content-stretch">
            <div class="grid grid-cols-1">
                <x-pane :header="__('coefs.coefficients')" prefix="">
                <div class="fields">
                    <div class="field">
                        <label class="required" for="time">{{ __('coefs.time') }}</label>
                        <input wire:model="time"
                               type="time" name="time"  />
                        <div class="validation-error">@error('time') {{ $message }} @enderror</div>
                    </div>
                    <div class="field">
                        <label class="required" for="k1">{{ __('coefs.k1') }}</label>
                        <input wire:model="k1"
                               type="number" min="0.01" step="0.01"
                               name="k1"  />
                        <div class="validation-error">@error('k1') {{ $message }} @enderror</div>
                    </div>
                    <div class="field">
                        <label class="required" for="k2">{{ __('coefs.k2') }}</label>
                        <input wire:model="k2"
                               type="number" min="0.0" step="0.01"
                               name="k2"  />
                        <div class="validation-error">@error('k2') {{ $message }} @enderror</div>
                    </div>
                    <div class="field">
                        <label class="required" for="k3">{{ __('coefs.k3') }}</label>
                        <input wire:model="k3"
                               type="number" min="0.01" step="0.01"
                               name="k3"  />
                        <div class="validation-error">@error('k3') {{ $message }} @enderror</div>
                    </div>
                </div>
                </x-pane>
            </div>
        <div class="hints">
            <div class="grid grid-cols-1">
                <x-pane :header="__('coefs.now_u_have')" prefix="">
                    <table class="table table-responsive">
                        <thead>
                        <tr>
                            <th>{{ __('coefs.time') }}</th>
                            <th>{{ __('coefs.k1') }}</th>
                            <th>{{ __('coefs.k2') }}</th>
                            <th>{{ __('coefs.k3') }}</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($factors as $factor)
                            <tr>
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
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </x-pane>
            </div>
        </div>
        <div class="button-horizontal">
            <button class="btn settings__btn-save primary" type="submit">{{ __('inputs.save') }}</button>
            <a class="btn settings__btn-cancel default" href="{{ route("factors") }}">{{ __('inputs.cancel') }}</a>
        </div>
        </div>
    </form>
</div>
