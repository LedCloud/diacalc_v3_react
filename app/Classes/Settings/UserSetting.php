<?php

namespace App\Classes\Settings;

class UserSetting
{
    public const CALORIE_NEAR = 300;

    public const DEFAULT = [
        'menu_info' => 152,
        'round_to' => 0,
        'is_plasma' => 1,
        'is_mmol' => 1,
        'target' => 5.6,
        'use_freq' => 1,
        'freq_qty' => 15,
        'filter_off' => 25,
        'k3_factor' => 187,
        'weight' => 60,
        'factors_by_time' => 0,
        'calory_limit' => 2000,
        'calorie_near' => self::CALORIE_NEAR,
        'low_level' => 4.0,
        'high_level' => 8,
        'period' => 7,
        'be' => 10,
    ];
}
