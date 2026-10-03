<?php

namespace App\Console\Commands;

use App\Console\Services\CopyService;

class DiacalcCopyProducts extends DiacalcCopyCommand
{
    protected $signature = 'diacalc:copy-products {--keep=*}';

    protected function descriptionKey(): string
    {
        return 'migration.commands.products';
    }

    public function handle(CopyService $copyService): int
    {
        if (($keep = $this->confirmedKeepEmails()) === null) {
            return self::FAILURE;
        }

        $copyService->copyProducts($this, $keep);

        return self::SUCCESS;
    }
}
