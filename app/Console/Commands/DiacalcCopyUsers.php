<?php

namespace App\Console\Commands;

use App\Console\Services\CopyService;

class DiacalcCopyUsers extends DiacalcCopyCommand
{
    protected $signature = 'diacalc:copy-users {--keep=*}';

    protected function descriptionKey(): string
    {
        return 'migration.commands.users';
    }

    public function handle(CopyService $copyService): int
    {
        if (($keep = $this->confirmedKeepEmails()) === null) {
            return self::FAILURE;
        }

        $copyService->copyUsers($this, $keep);

        return self::SUCCESS;
    }
}
