<?php

namespace App\Console\Commands;

use App\Console\Services\CopyService;

class DiacalcCopyDiary extends DiacalcCopyCommand
{
    protected $signature = 'diacalc:copy-diary {--clear-current}';

    protected function descriptionKey(): string
    {
        return 'migration.commands.diary';
    }

    public function handle(CopyService $copyService): int
    {
        $copyService->copyDiary($this, $this->shouldClearCurrent());

        return self::SUCCESS;
    }
}
