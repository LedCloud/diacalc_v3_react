<?php

namespace App\Console\Commands;

use App\Console\Services\CopyService;

class DiacalcCopyDiary extends DiacalcCopyCommand
{
    protected $signature = 'diacalc:copy-diary {--keep=*}';

    protected function descriptionKey(): string
    {
        return 'migration.commands.diary';
    }

    public function handle(CopyService $copyService): int
    {
        if (($keep = $this->confirmedKeepEmails()) === null) {
            return self::FAILURE;
        }

        $copyService->copyDiary($this, $keep);

        return self::SUCCESS;
    }
}
