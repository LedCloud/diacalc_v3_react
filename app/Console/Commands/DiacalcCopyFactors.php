<?php

namespace App\Console\Commands;

use App\Console\Services\CopyService;

class DiacalcCopyFactors extends DiacalcCopyCommand
{
    protected $signature = 'diacalc:copy-factors {--clear-current}';

    protected function descriptionKey(): string
    {
        return 'migration.commands.factors';
    }

    public function handle(CopyService $copyService): int
    {
        $copyService->copyFactors($this, $this->shouldClearCurrent());

        return self::SUCCESS;
    }
}
