<?php

namespace App\Console\Commands;

use App\Console\Services\CopyService;

class DiacalcCopyMenus extends DiacalcCopyCommand
{
    protected $signature = 'diacalc:copy-menus {--clear-current}';

    protected function descriptionKey(): string
    {
        return 'migration.commands.menus';
    }

    public function handle(CopyService $copyService): int
    {
        $copyService->copyMenus($this, $this->shouldClearCurrent());

        return self::SUCCESS;
    }
}
