<?php

namespace App\Classes;

use App\Http\Middleware\SetLocale;

class MenuStructure
{
    const structure = [
        [
            'route' => 'dashboard',
            'name' => 'dashboard',
        ],
        [
            'route' => 'archive',
            'name' => 'archive',
        ],
        [
            'name' => 'languages',
            'submenu' => [
                ['route' => 'language.switch', 'name'=>'en', 'method' => 'post', 'params' => ['lang' => 'en']],
                ['route' => 'language.switch', 'name'=>'ru', 'method' => 'post', 'params' => ['lang' => 'ru']],
            ],
        ],
        [
            'name' => 'settings',
            'submenu' => [
                ['route' => 'settings', 'name' => 'settings',],
                ['route' => 'factors', 'name' => 'factors', ],
                ['route' => 'calculations', 'name' => 'calculations', ],
            ],
        ],
    ];

    public static function getMenuStructure()
    {
        $structure = self::structure;

        foreach($structure as $key => $item) {
            if (array_key_exists('name', $item)) {
                $structure[$key]['name'] = __('menu_items.' . $item['name']);
            }
            if (array_key_exists('submenu', $item)) {
                foreach($item['submenu'] as $s_key => $s_item) {
                    if (array_key_exists('name', $s_item)) {
                        $structure[$key]['submenu'][$s_key]['name'] = __('menu_items.' . $s_item['name']);
                    }
                }
            }
        }

        return $structure;
    }
}
