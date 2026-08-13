<?php

namespace App\Console\Commands;

use App\Console\Services\CopyService;

class DiacalcCommon extends DiacalcCopyCommand
{
    protected $signature = 'diacalc:all {--clear-current}';

    protected function descriptionKey(): string
    {
        return 'migration.commands.all';
    }

    protected function clearCurrentOptionDescription(): string
    {
        return __('migration.clear_current_all');
    }

    public function handle(CopyService $copyService): int
    {
        $clear = $this->shouldClearCurrent();

        $copyService->copyArchive($this, $clear);
        $copyService->copyUsers($this, $clear);
        $copyService->copyEatings($this, $clear);
        $copyService->copyFactors($this, $clear);
        $copyService->copySettings($this, $clear);
        $copyService->copyProducts($this, $clear);
        $copyService->copyMenus($this, $clear);
        $copyService->copyDiary($this, $clear);

        $this->newLine();

        return self::SUCCESS;
    }
}
