<?php

namespace App\Console\Commands;

use App\Console\Services\CopyService;

class DiacalcCommon extends DiacalcCopyCommand
{
    protected $signature = 'diacalc:all {--keep=*}';

    protected function descriptionKey(): string
    {
        return 'migration.commands.all';
    }

    protected function keepOptionDescription(): string
    {
        return __('migration.keep_all');
    }

    public function handle(CopyService $copyService): int
    {
        if (($keep = $this->confirmedKeepEmails()) === null) {
            return self::FAILURE;
        }

        $copyService->copyArchive($this, $keep);
        $copyService->copyUsers($this, $keep);
        $copyService->copyEatings($this, $keep);
        $copyService->copyFactors($this, $keep);
        $copyService->copySettings($this, $keep);
        $copyService->copyProducts($this, $keep);
        $copyService->copyMenus($this, $keep);
        $copyService->copyDiary($this, $keep);

        $this->newLine();

        return self::SUCCESS;
    }
}
