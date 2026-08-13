<?php

namespace App\Console\Commands;

use App\Console\Services\CopyService;

class DiacalcCopyEatings extends DiacalcCopyCommand
{
    protected $signature = 'diacalc:copy-eatings {--clear-current}';

    protected function descriptionKey(): string
    {
        return 'migration.commands.eatings';
    }

    public function handle(CopyService $copyService): int
    {
        $copyService->copyEatings($this, $this->shouldClearCurrent());

        return self::SUCCESS;
    }
}
