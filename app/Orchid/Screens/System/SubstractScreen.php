<?php

namespace App\Orchid\Screens\System;

use Orchid\Screen\Screen;
use App\Orchid\Layouts\SubtractListener;

class SubstractScreen extends Screen
{
    /**
     * Display header name.
     *
     * @return string|null
     */
    public function name(): ?string
    {
        return __('Subs');
    }

    public function description(): ?string
    {
        return __('Substract');
    }

    public function layout(): iterable
    {
        $lsnr = new SubtractListener(5);
        return [
            $lsnr,
        ];
    }

    public function query(): iterable
    {
        return [
            'minuend' => 0,
            'subtrahend' => 0,
        ];
    }
}
