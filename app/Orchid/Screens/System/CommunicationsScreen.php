<?php

namespace App\Orchid\Screens\System;

use App\Classes\Settings\ContactEmailsSetting;
use App\Classes\Settings\TelegramSetting;
use App\Mail\TestEmailMd;
//use App\Mail\TestEmailWorkingMail;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Orchid\Screen\Actions\Button;
use Orchid\Screen\Fields\CheckBox;
use Orchid\Screen\Fields\Input;
use Orchid\Screen\Fields\Switcher;
use Orchid\Screen\Screen;
use Orchid\Screen\Repository;
use Orchid\Support\Facades\Layout;
use Illuminate\Http\Request;
use Orchid\Support\Facades\Toast;

class CommunicationsScreen extends Screen
{

    /**
     * Display header name.
     *
     * @return string|null
     */
    public function name(): ?string
    {
        return __('Communication settings');
    }

    public function description(): ?string
    {
        return __('Select the different ways to receive the notifications');
    }

    public function commandBar(): iterable
    {
        return [];
    }

    /**
     * Views.
     *
     * @return \Orchid\Screen\Layout[]|string[]
     */
    public function layout(): iterable
    {
        return [
            Layout::rows([

                Switcher::make('Emails.use')
                    ->title('Checkbox')
                    //->checked()
                    ->sendTrueOrFalse()
                    ->placeholder('Use email'),
                Input::make('Emails.emails')
                    ->title(__('Contact emails'))
                    ->help(__('emails, comma separated, to which the notifications will be sent')),
                Button::make(__('Save'))
                    ->icon('check')
                    ->method('saveEmailSetting'),

            ])->title(__('Contact emails')),

            Layout::rows([
                Input::make('test.email')
                    ->title('Email')
                    ->type('email')
                    ->help('email address to send a test email to'),

                Button::make(__('Send Email'))
                    ->icon('check')
                    ->method('testEmail'),

                Button::make('Проверить отправку в Telegram')
                    ->icon('check')
                    ->method('testTelegram'),
            ])->title(__('Test communication channels')),
        ];
    }
//Mail::to($request->user())->send(new OrderShipped($order));

    public function testEmail(Request $request)
    {
        $validated = $request->validate([
            'test.email' => 'required|string|email|regex:/(.+)@(.+)\.(.+)/i',
        ]);

        $email = $validated['test']['email'];

        Mail::to($email)
            ->send(new TestEmailMd());

        Toast::info(__('Test email was sent'));

    }

    public function saveEmailSetting(Request $request)
    {
        $validated = $request->validate([
            'Emails.emails' => 'required|string',
            'Emails.use' => 'bool',
        ]);

        (new ContactEmailsSetting())->setValues($validated['Emails']);

        Toast::info(__('Test email was saved'));
    }
    /**
     * Query data.
     *
     * @return array
     */
    public function query(): iterable
    {
        return [
            //'reCaptcha' => new Repository((new ReCaptcha())->getValues()),
            'Emails' => new Repository((new ContactEmailsSetting())->getValues()),
            'telegram' => new Repository((new TelegramSetting())->getValues()),
        ];
    }

}
