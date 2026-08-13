<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

abstract class DiacalcCopyCommand extends Command
{
    abstract protected function descriptionKey(): string;

    public function getOutput()
    {
        return $this->output;
    }

    protected function configure(): void
    {
        parent::configure();

        $this->setDescription(__($this->descriptionKey()));

        if ($this->getDefinition()->hasOption('clear-current')) {
            $this->getDefinition()
                ->getOption('clear-current')
                ->setDescription($this->clearCurrentOptionDescription());
        }
    }

    protected function clearCurrentOptionDescription(): string
    {
        return __('migration.clear_current');
    }

    protected function shouldClearCurrent(): bool
    {
        return (bool) $this->option('clear-current');
    }
}
