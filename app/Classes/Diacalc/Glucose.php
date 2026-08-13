<?php

namespace App\Classes\Diacalc;

use Illuminate\Support\Facades\Log;

class Glucose
{
    protected static $settings = null;

    protected $value;

    protected $is_mmol = null;
    protected $is_plasma = null;

    /**
     * Value is always in mmol and whole
     * @param $value
     */
    public function __construct($value)
    {
        static::init();
        $this->value = $value;
    }

    protected static function init()
    {
        if (!isset(static::$settings)) {
            static::$settings = auth()->user()->getSetting('User');
        }
    }

    public static function convertToRaw($value, $is_mmol = null, $is_plasma = null)
    {
        static::init();

        if (!isset($is_mmol))
        {
            $is_mmol = static::$settings['is_mmol'];
        }

        if (!isset($is_plasma)) {
            $is_plasma = static::$settings['is_plasma'];
        }

        return $value / (
                ($is_plasma ? 1.12 : 1) * ($is_mmol ? 1 : 18)
            );
    }

    public function getRawValue()
    {
        return $this->value;
    }

    public function setGlucose($value): Glucose
    {
        static::init();

        $this->initMmol();
        $this->initPlasma();

        //convert to mmol and whole
        $this->value = $value / $this->getFactors();

        return $this;
    }

    protected function getFactors()
    {
        $this->initMmol();
        $this->initPlasma();

        return($this->is_plasma ? 1.12 : 1) * ($this->is_mmol ? 1 : 18);
    }

    protected function initMmol()
    {
        if (!isset($this->is_mmol)) {
            $this->is_mmol = static::$settings['is_mmol'];
        }
    }

    protected function initPlasma()
    {
        if (!isset($this->is_plasma)) {
            $this->is_plasma = static::$settings['is_plasma'];
        }
    }

    public function getForView($decimal_separator = null)
    {
        if (empty($decimal_separator)) {
            $decimal_separator = '.';
        }

        $vl = $this->value * $this->getFactors();

        if ($this->is_mmol) {
            return number_format($vl, 1, $decimal_separator, '');

        }
        return number_format($vl, 0, $decimal_separator, '');
    }

    public function setMmol(bool $is_mmol): Glucose
    {
        $this->is_mmol = $is_mmol;

        return $this;
    }

    public function setPlasma(bool $is_plasma): Glucose
    {
        $this->is_plasma = $is_plasma;

        return $this;
    }
}
