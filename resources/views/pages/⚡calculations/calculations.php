<?php

use App\Classes\Diacalc\Glucose;
use Illuminate\Support\Facades\Log;
use Livewire\Component;

new class extends Component
{
    public $av = 0;
    public $bv = 0;

    public float $mmol_whole;
    public float $mmol_plasma;
    public float $mgdl_whole;
    public float $mgdl_plasma;
    public float $hbac;

    protected $gl;

    public function boot()
    {
        $this->gl = (new Glucose(5.6))
            ->setPlasma(false)
            ->setMmol(true);

        $this->mmol_whole = $this->getMmolWhole($this->gl);
        $this->mmol_plasma = $this->getMmolPlasma($this->gl);
        $this->mgdl_plasma = $this->getMgdlPlasma($this->gl);
        $this->mgdl_whole = $this->getMgdlWhole($this->gl);

        $this->hbac = 5;

        $this->av = 2;
        $this->bv = 4;
    }

    public function mount()
    {
        $this->mmol_whole = $this->getMmolWhole($this->gl);
        $this->mmol_plasma = $this->getMmolPlasma($this->gl);
        $this->mgdl_plasma = $this->getMgdlPlasma($this->gl);
        $this->mgdl_whole = $this->getMgdlWhole($this->gl);
    }

    public function updatedAv($value)
    {
        $this->bv = $this->av * 2;
    }

    public function updatedBv($value)
    {
        $this->av = intval($this->bv /2);
    }

    public function updated($property, $value) {

        return;

        $this->$property = $value;

        if ($property == 'av') {
            $this->bv = $this->av * 2;
        } else {
            $this->av = intval($this->bv / 2);
        }

        return

        Log::info('Updated', [$property, $value]);

        if (in_array($property, [
            'mmol_whole',
            'mmol_plasma',
            'mgdl_whole',
            'mgdl_plasma',
            'hbac'])) {

            //$this->$property = $value;

            //$gl = new Glucose(5.6);

            switch ($property) {
                case 'mmol_whole':
                    $this->gl->setMmol(true)->setPlasma(false)->setGlucose($value);
//                    $this->mmol_plasma = $this->getMmolPlasma($gl);
//                    $this->mgdl_whole = $this->getMgdlPlasma($gl);
//                    $this->mgdl_plasma = $this->getMgdlWhole($gl);
//                    $this->hbac = 5.5;
                    break;
                case 'mmol_plasma':
                    $this->gl->setMmol(true)->setPlasma(true)->setGlucose($value);
//                    $this->mgdl_whole = $this->getMmolWhole($gl);
//                    $this->mgdl_whole = $this->getMgdlPlasma($gl);
//                    $this->mgdl_plasma = $this->getMgdlWhole($gl);
//                    $this->hbac = 5.5;
                    break;
                case 'mgdl_whole':
                    $this->gl->setMmol(false)->setPlasma(false)->setGlucose($value);
//                    $this->mgdl_whole = $this->getMmolWhole($gl);
//                    $this->mmol_plasma = $this->getMmolPlasma($gl);
//                    $this->mgdl_plasma = $this->getMgdlWhole($gl);
//                    $this->hbac = 5.5;
                    break;
                case 'mgdl_plasma':
                    $this->gl->setMmol(false)->setPlasma(true)->setGlucose($value);
//                    $this->mmol_whole  = $this->getMmolWhole($gl);
//                    $this->mmol_plasma = $this->getMmolPlasma($gl);
//                    $this->mgdl_whole = $this->getMmolWhole($gl);
//                    $this->hbac = 5.5;
                    break;
            }


            if ($property !== 'mmol_whole')
                $this->mmol_whole = $this->getMmolWhole($this->gl);
            if ($property !== 'mmol_plasma')
                $this->mmol_plasma = $this->getMmolPlasma($this->gl);
            if ($property !== 'mgdl_plasma')
                $this->mgdl_plasma = $this->getMgdlPlasma($this->gl);
            if ($property !== 'mgdl_whole')
                $this->mgdl_whole = $this->getMgdlWhole($this->gl);

            $this->hbac = rand(1,100);

            Log::info('After calc', [
                $this->mmol_whole,
                $this->mmol_plasma,
                $this->mgdl_whole,
                $this->mgdl_plasma,
            ]);
        }
    }

    protected function getMmolWhole(Glucose $gl) {
        return $gl
            ->setPlasma(false)
            ->setMmol(true)
            ->getForView();
    }

    protected function getMmolPlasma(Glucose $gl) {
        return $gl
            ->setPlasma(true)
            ->setMmol(true)
            ->getForView();
    }

    protected function getMgdlPlasma(Glucose $gl) {
        return $gl
            ->setPlasma(true)
            ->setMmol(false)
            ->getForView();
    }

    protected function getMgdlWhole(Glucose $gl) {
        return $gl
            ->setPlasma(false)
            ->setMmol(false)
            ->getForView();
    }
};
