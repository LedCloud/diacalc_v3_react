<div>
    <form class="settings__form" wire:submit="save">
        <div class="tabs">
            <x-tabs.tag :active="$section === $this::MENU" :section="$this::MENU">
                {{ __('settings.menu') }}
            </x-tabs.tag>
            <x-tabs.tag :active="$section === $this::GLUCOSE" :section="$this::GLUCOSE">
                {{ __('settings.glucose') }}
            </x-tabs.tag>
            <x-tabs.tag :active="$section === $this::PRODUCTS" :section="$this::PRODUCTS">
                {{ __('settings.products') }}
            </x-tabs.tag>

                    <div class="tab-content menu-panes">

                        <x-pane :header="__('settings.menu_info')"
                            :active="$section === $this::MENU">

                            <fieldset>
                                <legend>{{ __('settings.shown_info') }}</legend>
                                <label for="menu-prot">
                                    <input id="menu-prot"
                                           wire:model.live="selectedMasks"
                                           value="{{ \App\Classes\Settings\MenuInfo::MASK_PROT }}"
                                           type="checkbox"/>{{ __('settings.proteins') }}</label>
                                <label for="menu-fat">
                                    <input id="menu-fat"
                                           wire:model.live="selectedMasks"
                                           value="{{ \App\Classes\Settings\MenuInfo::MASK_FAT }}"
                                           type="checkbox"/>{{ __('settings.fats') }}</label>
                                <label for="menu-carb">
                                    <input id="menu-carb"
                                           wire:model.live="selectedMasks"
                                           value="{{ \App\Classes\Settings\MenuInfo::MASK_CARB }}"
                                           type="checkbox"/>{{ __('settings.carbs') }}</label>
                                <label for="menu-be">
                                    <input id="menu-be"
                                           wire:model.live="selectedMasks"
                                           value="{{ \App\Classes\Settings\MenuInfo::MASK_BE }}"
                                           type="checkbox"/>{{ __('settings.be') }}</label>
                                <label for="menu-dose">
                                    <input id="menu-dose"
                                           wire:model.live="selectedMasks"
                                           value="{{ \App\Classes\Settings\MenuInfo::MASK_DOSE }}"
                                           type="checkbox"/>{{ __('settings.dose') }}</label>
                                <label for="menu-gi">
                                    <input id="menu-gi"
                                           wire:model.live="selectedMasks"
                                           value="{{ \App\Classes\Settings\MenuInfo::MASK_GI }}"
                                           type="checkbox"/>{{ __('settings.gi') }}</label>
                                <label for="menu-gl">
                                    <input id="menu-gl"
                                           wire:model.live="selectedMasks"
                                           value="{{ \App\Classes\Settings\MenuInfo::MASK_GL }}"
                                           type="checkbox"/>{{ __('settings.gl') }}</label>
                                <label for="menu-calory">
                                    <input id="menu-calory"
                                           wire:model.live="selectedMasks"
                                           value="{{ \App\Classes\Settings\MenuInfo::MASK_CALOR }}"
                                           type="checkbox"/>{{ __('settings.calories') }}</label>
                            </fieldset>
                            <fieldset>
                                <label for="round-dose">{{ __('settings.round') }}</label>
                                <select id="round-dose" wire:model.live="selectRound">
                                    <option value="0">{{ __('settings.round_int') }}</option>
                                    <option value="1">{{ __('settings.round_half') }}</option>
                                    <option value="2">{{ __('settings.round_quarter') }}</option>
                                </select>
                                <label for="calorie_limit">{{ __('settings.calorie_limit') }}</label>
                                <input id="calorie_limit" type="number" min="1" step="1" wire:model="calory_limit"/>
                                <div class="validation-error">@error('calory_limit') {{ $message }} @enderror</div>
                            </fieldset>
                        </x-pane>

                        <x-pane :header="__('settings.glucose')"
                                :active="$section === $this::GLUCOSE">

                                <fieldset>
                                    <label for="plasma">
                                        <input type="radio"
                                               name="plasma"
                                            id="plasma"
                                            wire:model.live="plasma"
                                           value="{{ $this::WHOLE }}" />{{ __('settings.plasma') }}</label>
                                    <label for="whole">
                                        <input type="radio" name="plasma"
                                           id="whole"
                                           wire:model.live="plasma"
                                               value="{{ $this::PLASMA }}" />{{ __('settings.whole') }}</label>
                                </fieldset>
                                <fieldset>
                                    <label for="mmol">
                                        <input type="radio" name="mmol"
                                               id="mmol"
                                               wire:model.live="mmol"
                                               value="{{ $this::MMOL }}" />{{ __('settings.mmol') }}</label>
                                    <label for="mgdl">
                                        <input type="radio"
                                               name="mmol"
                                               id="mgdl"
                                               wire:model.live="mmol"
                                               value="{{ $this::MGDL }}" />{{ __('settings.mgdl') }}</label>
                                </fieldset>

                                <div class="field" wire:key="target-id-{{ $plasma }}-{{ $mmol }}">
                                    <label for="target">{{ __('settings.target') }}</label>
                                    <input id="target" type="number" step="0.1" min="3"
                                           wire:model.live.debounce.250ms="target"/>
                                    <div class="validation-error">@error('target') {{ $message }} @enderror</div>
                                </div>

                                <div class="field" wire:key="low-level-id-{{ $plasma }}-{{ $mmol }}">
                                    <label for="low_level">{{ __('settings.low_level') }}</label>
                                    <input id="low_level" type="number" step="0.1" min="3"
                                           wire:model.live.debounce.250ms="low_level"/>
                                    <div class="validation-error">@error('low_level') {{ $message }} @enderror</div>
                                </div>

                                <div class="field" wire:key="high-level-id-{{ $plasma }}-{{ $mmol }}">
                                    <label for="high_level">{{ __('settings.high_level') }}</label>
                                    <input id="high_level" type="number" step="0.1" min="3"
                                           wire:model.live.debounce.250ms="high_level"/>
                                    <div class="validation-error">@error('high_level') {{ $message }} @enderror</div>
                                </div>
                        </x-pane>

                        <x-pane :header="__('settings.products')"
                                :active="$section === $this::PRODUCTS">
                                <fieldset>
                                    <legend>{{ __('settings.fill_default') }}</legend>
                                    <button class="btn settings__btn-fill default" type="button"
                                            wire:click="fillProducts">{{ __('settings.fill') }}</button>
                                </fieldset>
                                <label for="use_freq">
                                    <input type="checkbox" id="use_freq" wire:model="use_freq">
                                    {{ __('settings.use_freq') }}
                                </label>
                                <div class="field">
                                    <label for="freq_qty">{{ __('settings.freq_qty') }}</label>
                                    <input id="freq_qty" type="number"
                                           wire:model="freq_qty"/>
                                    <div class="validation-error">@error('freq_qty') {{ $message }} @enderror</div>
                                </div>
                                <div class="field">
                                    <label for="filter_off">{{ __('settings.filter_off') }}</label>
                                    <input id="filter_off" type="number"
                                           wire:model="filter_off"/>
                                    <div class="validation-error">@error('filter_off') {{ $message }} @enderror</div>
                                </div>
                        </x-pane>
                    </div>

                    </div>
        <div class="button-horizontal">
            <button class="btn settings__btn-save primary" type="submit">{{ __('inputs.save') }}</button>
            <a class="btn settings__btn-calcel default" href="{{ route("dashboard") }}">{{ __('inputs.cancel') }}</a>
        </div>
    </form>

    <x-toast-msg />
</div>
