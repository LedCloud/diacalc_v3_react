<?php

namespace App\Console\Commands;

use App\Console\Services\CopyService;

class DiacalcCopySettings extends DiacalcCopyCommand
{
    protected $signature = 'diacalc:copy-settings {--clear-current}';

    protected function descriptionKey(): string
    {
        return 'migration.commands.settings';
    }

    public function handle(CopyService $copyService): int
    {
        $copyService->copySettings($this, $this->shouldClearCurrent());

        return self::SUCCESS;
    }
}
