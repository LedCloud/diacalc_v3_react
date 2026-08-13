
<div>
    <div class="calculations-layout">
        <div class="calculations-layout__top">
            <x-pane :header="__('calcs.glucose')" prefix="calcs">
                <div class="horizontal-group">
                    <label>{{ __('calcs.whole') }}</label>
                    <input wire:model.blur="av"  wire:key="input-av"  />
                    <label>{{ __('calcs.mmol') }}</label>
                </div>
                <div class="horizontal-group">
                    <label>{{ __('calcs.plasma') }} {{ $av }}*{{ $bv }}</label>
                    <input wire:model.blur="bv"  wire:key="input-bv"  />
                    <label>{{ __('calcs.mmol') }}</label>
                </div>
                <div class="horizontal-group">
                    <label>{{ __('calcs.whole') }}</label>
                    <input wire:model.live.debounce.250ms="mgdl_whole" />
                    <label>{{ __('calcs.mgdl') }}</label>
                </div>
                <div class="horizontal-group">
                    <label>{{ __('calcs.plasma') }}</label>
                    <input wire:model.live.debounce.250ms="mgdl_plasma" />
                    <label>{{ __('calcs.mgdl') }}</label>
                </div>
                <div class="horizontal-group">
                    <label>{{ __('calcs.hbac') }}</label>
                    <input wire:model.live.debounce.250ms="hbac" />
                    <label>{{ __('calcs.percent') }}</label>
                </div>
            </x-pane>
            <x-pane :header="__('calcs.glycemy')" prefix="calcs">
                right
            </x-pane>
        </div>
        <div class="calculations-layout__bottom">
            <x-pane :header="__('calc.weigth')" prefix="coefs">
                Bottom
            </x-pane>
        </div>
    </div>
</div>
