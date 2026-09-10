<?php

uses(Tests\TestCase::class);

it('renders a static bilingual maintenance page', function () {
    $html = view('errors.503')->render();

    expect($html)
        ->toContain('The application is now in maintenance mode')
        ->toContain('режиме технического обслуживания')
        ->not->toContain('@vite');
});
