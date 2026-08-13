<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Livewire\Component;
use App\Classes\Settings\MenuInfo;
use App\Classes\Diacalc\Glucose;
use Livewire\Attributes\On;
use Livewire\Attributes\Validate;

new class extends Component {
    const MENU = 'menu';
    const GLUCOSE = 'glucose';
    const PRODUCTS = 'products';

    const PLASMA = 'plasma';
    const WHOLE = 'whole';

    const MMOL = 'mmol';
    const MGDL = 'mgdl';

    public $section = '';

    public $settings = null;
    public array $selectedMasks = [];
    public int $selectRound = 0;

    #[Validate('integer|min:1000')]
    public int $calory_limit = 0;

    #[Validate('numeric|min:3')]
    public float $target = 0;

    #[Validate('numeric|min:3')]
    public float $low_level = 0;

    #[Validate('numeric|min:3')]
    public float $high_level = 0;

    public $plasma;
    public $mmol;

    public $use_freq;

    #[Validate('integer|min:5')]
    public $freq_qty;

    #[Validate('integer|min:5|max:50')]
    public $filter_off;

    protected ?Glucose $gl_target;
    protected ?Glucose $gl_low;
    protected ?Glucose $gl_high;

    protected array $gls = [];

    protected function isMmol()
    {
        return $this->mmol === self::MMOL;
    }

    protected function isPlasma()
    {
        return $this->plasma === self::PLASMA;
    }

    /**
     * This is called first
     * @return void
     */
    public function boot()
    {
        $settings = Auth::user()->getSetting('User');

        $this->gl_target =
            (new Glucose($settings['target']))
            ->setMmol($settings['is_mmol'])
            ->setPlasma($settings['is_plasma']);

        $this->gl_low =
            (new Glucose($settings['low_level']))
                ->setMmol($settings['is_mmol'])
                ->setPlasma($settings['is_plasma']);

        $this->gl_high =
            (new Glucose($settings['high_level']))
                ->setMmol($settings['is_mmol'])
                ->setPlasma($settings['is_plasma']);

        $this->gls = [
            'target' => $this->gl_target,
            'low' => $this->gl_low,
            'high' => $this->gl_high];
    }

    /**
     * This is called multiple times afterward
     * @return void
     */
    public function mount()
    {
        $this->section = self::MENU;
        $this->settings = Auth::user()->getSetting('User');

        foreach (MenuInfo::getAll() as $bit) {
            if ($this->settings['menu_info'] & $bit) {
                $this->selectedMasks[] = (string)$bit;
            }
        }

        $this->plasma = $this->settings['is_plasma'] ? self::PLASMA : self::WHOLE;
        $this->mmol = $this->settings['is_mmol'] ? self::MMOL : self::MGDL;

        $this->selectRound = $this->settings['round_to'];
        $this->calory_limit = $this->settings['calory_limit'];

        $this->target = $this->gl_target->getForView();
        $this->low_level = $this->gl_low->getForView();
        $this->high_level = $this->gl_high->getForView();

        $this->use_freq = (bool)$this->settings['use_freq'];
        $this->freq_qty = $this->settings['freq_qty'];
        $this->filter_off = $this->settings['filter_off'];
    }

    public function updated($property, $value)
    {
        if (in_array($property, ['plasma', 'mmol'])
            && !empty($value)
        ) {
            $this->$property = $value;

            foreach ($this->gls as $key => $gl) {
                $this->gls[$key]
                    ->setPlasma($this->isPlasma())
                    ->setMmol($this->isMmol());
            }

            //Recalculate all glucoses
            $this->target = $this->gl_target->getForView();
            $this->low_level = $this->gl_low->getForView();
            $this->high_level = $this->gl_high->getForView();

            $this->settings['is_plasma'] = $this->isPlasma();
            $this->settings['is_mmol'] = $this->isMmol();

        } elseif (in_array($property, ['target', 'low_level', 'high_level'])) {
            //we get mg or whole. it depends on the check-boxes, so we need to recalculate to raw value
            $gl = (new Glucose(5.6))
                ->setMmol($this->isMmol())
                ->setPlasma($this->isPlasma())
                ->setGlucose($value);

            $this->$property = $gl->getRawValue();
            switch ($property) {
                case 'target': $this->target = $gl->getForView();
                    $this->settings['target'] = $gl->getRawValue();
                break;
                case 'low_level': $this->low_level = $gl->getForView();
                    $this->settings['low_level'] = $gl->getRawValue();
                break;
                case 'high_level': $this->high_level = $gl->getForView();
                    $this->settings['high_level'] = $gl->getRawValue();
                break;
            }
        }
    }

    public function fillProducts()
    {
        $params = [
            'callbackOK' => 'fill-confirmed',
            'title' => __('settings.fill_msg_title'),
            'message' => __('settings.fill_msg_desc'),
            'ok' => __('settings.fill'),
        ];
        $this->dispatch("show-dialog", ['params' => $params]);
    }

    public function updatedSelectedMasks()
    {
        $this->settings['menu_info'] = array_sum($this->selectedMasks);
    }

    public function save()
    {
        $this->validate();

        $this->settings['calory_limit'] = $this->calory_limit;
        $this->settings['round_to'] = $this->selectRound;

        $this->settings['use_freq'] = (int)$this->use_freq;
        $this->settings['freq_qty'] = $this->freq_qty;
        $this->settings['filter_off'] = $this->filter_off;

        Auth::user()->putSetting('User', $this->settings);

        $this->dispatch('notify',
            message: __('settings.saved'),
            type: 'info'
        );
    }

    #[On('fill-confirmed')]
    public function fillConfirmed()
    {
        //clear all current products and fill with json DB
        Log::info('Let us clear and fill');
        //TODO add actual logic here
    }
};
