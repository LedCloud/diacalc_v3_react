<?php

namespace App\Console\Commands;

use App\Console\Services\CopyService;

class DiacalcCopySettings extends DiacalcCopyCommand
{
    protected $signature = 'diacalc:copy-settings {--keep=*}';

    protected function descriptionKey(): string
    {
        return 'migration.commands.settings';
    }

    public function handle(CopyService $copyService): int
    {
        if (($keep = $this->confirmedKeepEmails()) === null) {
            return self::FAILURE;
        }

        $copyService->copySettings($this, $keep);

        return self::SUCCESS;
    }
}
