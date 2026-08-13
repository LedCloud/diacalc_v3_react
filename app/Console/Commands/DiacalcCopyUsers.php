<?php

namespace App\Console\Commands;

use App\Console\Services\CopyService;

class DiacalcCopyUsers extends DiacalcCopyCommand
{
    protected $signature = 'diacalc:copy-users {--clear-current}';

    protected function descriptionKey(): string
    {
        return 'migration.commands.users';
    }

    public function handle(CopyService $copyService): int
    {
        $copyService->copyUsers($this, $this->shouldClearCurrent());

        return self::SUCCESS;
    }
}
