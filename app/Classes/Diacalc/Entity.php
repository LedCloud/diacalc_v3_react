<?php

namespace App\Classes\Diacalc;

class Entity
{
    protected static $ins = null;

    private function __construct(
        protected $settings
    )
    {}
}
