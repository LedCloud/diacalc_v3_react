<?php

namespace App\Console\Commands;

use App\Console\Services\CopyService;

class DiacalcCopyMenus extends DiacalcCopyCommand
{
    protected $signature = 'diacalc:copy-menus {--keep=*}';

    protected function descriptionKey(): string
    {
        return 'migration.commands.menus';
    }

    public function handle(CopyService $copyService): int
    {
        if (($keep = $this->confirmedKeepEmails()) === null) {
            return self::FAILURE;
        }

        $copyService->copyMenus($this, $keep);

        return self::SUCCESS;
    }
}
