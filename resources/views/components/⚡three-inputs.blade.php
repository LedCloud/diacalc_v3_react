<?php

use Illuminate\Support\Facades\Log;
use Livewire\Component;
use App\Classes\Diacalc\Glucose;

new class extends Component {
    const PLASMA = 'plasma';
    const MMOL = 'mmol';

    public $number;
    public $plasma;
    public $mmol;

    protected ?Glucose $gl = null;


    public function boot()
    {
        $this->gl = new Glucose(5.6);
        $this->gl->setMmol($this->isMmol());
        $this->gl->setPlasma($this->isPlasma());
    }

    protected function isMmol()
    {
        return $this->mmol === self::MMOL;
    }

    protected function isPlasma()
    {
        return $this->plasma === self::PLASMA;
    }

    public function mount()
    {
        $this->number = $this->gl->getForView();
        $this->plasma = self::PLASMA;
        $this->mmol = self::MMOL;
    }

    public function updated($prop, $vl) {
        $this->$prop = $vl;

        $this->gl->setPlasma($this->isPlasma());
        $this->gl->setMmol($this->isMmol());
        $this->number = $this->gl->getForView();
    }
};
?>

<div>
    <div style="display: flex; flex-direction: column">
        <label for="one">Number</label>
        <input id="one" type="number"
               wire:model="number"
               wire:key="{{ $plasma }}-{{ $mmol }}"
        />
        <fieldset style="display: flex; flex-direction: column;">
            <legend>Plasma <-> Whole ({{ $plasma }})</legend>
            <input type="radio"
                   name="plasma"
                   wire:model.live="plasma" value="{{ $this::PLASMA }}"
                   />
            <input type="radio"
                   name="plasma"
                   wire:model.live="plasma" value="whole"
                   />
        </fieldset>
        <fieldset style="display: flex; flex-direction: column;">
            <legend>Mmol <-> Mg/dl ({{ $mmol }})</legend>
            <input type="radio"
                   name="mmol"
                   wire:model.live="mmol" value="{{ $this::MMOL }}"
                   id="low"/>
            <input type="radio"
                   name="mmol"
                   wire:model.live="mmol" value="mgdl"
                   />
        </fieldset>
    </div>
</div>
