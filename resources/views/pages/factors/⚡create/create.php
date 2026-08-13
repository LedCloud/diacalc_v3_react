<?php

use App\Classes\Diacalc\Glucose;
use App\Models\Factor;

use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\Attributes\Validate;
use Illuminate\Http\Request;
use Livewire\Attributes\Locked;

new class extends Component
{
    #[Locked]
    public $id;

    #[Validate('required|string')]
    public $time;

    #[Validate('required|numeric|min:0.01')]
    public $k1;

    #[Validate('required|numeric|min:0.0')]
    public $k2;

    #[Validate('required|numeric|min:0.01')]
    public $k3;

    public $factors;

    protected $factor = null;

    public function boot()
    {
        if (!empty($this->id)) {

            Auth::user()->factors()->where('id', $this->id)->firstOrFail();

            $this->factor = Factor::findOrFail($this->id);

            $this->time = $this->factor->time->format('H:i');
            $this->k1 = $this->factor->k1;
            $this->k2 = $this->factor->k2;
            $this->k3 = (new Glucose($this->factor->k3))->getForView();
        } else {
            $this->k1 = 1;
            $this->k2 = 0;
            $this->k3 = 2;
        }
    }

    public function mount()
    {
        $this->factors = Auth::user()->factors;
    }

    public function save()
    {
        $this->validate();
        //create factors

        $data = [
            'time' => $this->time,
            'k1' => $this->k1,
            'k2' => $this->k2,
            'k3' => (new Glucose(5.6))
                ->setGlucose($this->k3)
                ->getRawValue(),
        ];

        $res = false;

        if ($this->id) {

            Factor::where('id', $this->id)->where('user_id', Auth::id())->update($data);


            $this->dispatch('notify',
                message: __('coefs.updated'),
                type: 'info'
            );

        } else {
            Auth::user()->factors()->create($data);

            $this->dispatch('notify',
                message: __('coefs.created'),
                type: 'info'
            );
        }

        redirect()->route('factors', [

        ]);
    }
};
