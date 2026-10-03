<?php

namespace App\Console\Commands;

use App\Console\Services\CopyService;

class DiacalcCopyArchive extends DiacalcCopyCommand
{
    protected $signature = 'diacalc:copy-archive {--keep=*}';

    protected function descriptionKey(): string
    {
        return 'migration.commands.archive';
    }

    public function handle(CopyService $copyService): int
    {
        $copyService->copyArchive($this, $this->keptEmails());

        return self::SUCCESS;
    }
}
