<?php

namespace App\Console\Commands;

use App\Console\Services\CopyService;

class DiacalcCopyEatings extends DiacalcCopyCommand
{
    protected $signature = 'diacalc:copy-eatings {--keep=*}';

    protected function descriptionKey(): string
    {
        return 'migration.commands.eatings';
    }

    public function handle(CopyService $copyService): int
    {
        if (($keep = $this->confirmedKeepEmails()) === null) {
            return self::FAILURE;
        }

        $copyService->copyEatings($this, $keep);

        return self::SUCCESS;
    }
}
