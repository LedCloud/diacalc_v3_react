<?php

use App\Classes\Diacalc\Glucose;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Livewire\Component;
use Livewire\Attributes\Validate;
use Livewire\Attributes\Computed;

new class extends Component
{
    public $settings = null;

    #[Validate('integer|min:1')]
    public $weight;

    public $factors_by_time;

    #[Validate('numeric|min:1')]
    public $k3_factor;

    public Collection $factors;

    public function mount()
    {
        $this->settings = Auth::user()->getSetting('User');

        $this->factors = Auth::user()->factors;

        $this->weight = $this->settings['weight'];
        $this->k3_factor = $this->settings['k3_factor'];
        $this->factors_by_time = (bool)$this->settings['factors_by_time'];
    }

    public function calculateFactors()
    {
        $this->dispatch('notify',
            message: __('coefs.factors_calculated'),
            type: 'info'
        );
    }

    public function deleteFactor($id)
    {
        if (\App\Models\Factor::destroy($id)) {
            $this->dispatch('notify',
                message: __('coefs.deleted'),
                type: 'error'
            );
        }
    }

    public function save()
    {
        $this->validate();
        $this->settings['factors_by_time'] = (int)$this->factors_by_time;
        $this->settings['k3_factor'] = $this->k3_factor;
        $this->settings['weight'] = $this->weight;

        Auth::user()->putSetting('User', $this->settings);

        $this->dispatch('notify',
            message: __('coefs.saved'),
            type: 'info'
        );
    }
};
