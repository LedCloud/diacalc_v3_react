<?php

namespace App\Console\Commands;

use App\Console\Services\CopyService;

class DiacalcCopyArchive extends DiacalcCopyCommand
{
    protected $signature = 'diacalc:copy-archive {--clear-current}';

    protected function descriptionKey(): string
    {
        return 'migration.commands.archive';
    }

    public function handle(CopyService $copyService): int
    {
        $copyService->copyArchive($this, $this->shouldClearCurrent());

        return self::SUCCESS;
    }
}
