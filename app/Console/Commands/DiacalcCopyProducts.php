<?php

namespace App\Console\Commands;

use App\Console\Services\CopyService;

class DiacalcCopyProducts extends DiacalcCopyCommand
{
    protected $signature = 'diacalc:copy-products {--clear-current}';

    protected function descriptionKey(): string
    {
        return 'migration.commands.products';
    }

    public function handle(CopyService $copyService): int
    {
        $copyService->copyProducts($this, $this->shouldClearCurrent());

        return self::SUCCESS;
    }
}
