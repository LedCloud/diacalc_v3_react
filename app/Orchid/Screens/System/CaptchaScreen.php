<?php

namespace App\Orchid\Screens\System;

use App\Classes\Settings\ContactEmailsSetting;
use App\Classes\Settings\TelegramSetting;
use Orchid\Screen\Repository;
use Orchid\Screen\Screen;

class CaptchaScreen extends Screen
{
    /**
     * Display header name.
     *
     * @return string|null
     */
    public function name(): ?string
    {
        return __('Product archive');
    }

    public function description(): ?string
    {
        return __('This product archive is sharable between all users');
    }

    public function commandBar(): iterable
    {
        return [];
    }

    public function layout(): iterable
    {

    }

    /**
     * Query data.
     *
     * @return array
     */
    public function query(): iterable
    {
        return [
            'groups' => new Repository((new ContactEmailsSetting())->getValues()),
            'telegram' => new Repository((new TelegramSetting())->getValues()),
        ];
    }
}
