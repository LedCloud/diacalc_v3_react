<?php

namespace App\Console\Commands;

use App\Console\Services\CopyService;

class DiacalcCopyFactors extends DiacalcCopyCommand
{
    protected $signature = 'diacalc:copy-factors {--keep=*}';

    protected function descriptionKey(): string
    {
        return 'migration.commands.factors';
    }

    public function handle(CopyService $copyService): int
    {
        if (($keep = $this->confirmedKeepEmails()) === null) {
            return self::FAILURE;
        }

        $copyService->copyFactors($this, $keep);

        return self::SUCCESS;
    }
}
