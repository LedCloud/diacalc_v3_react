<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

abstract class DiacalcCopyCommand extends Command
{
    abstract protected function descriptionKey(): string;

    public function __construct()
    {
        $this->signature = str_replace(
            '{--keep=*}',
            '{--keep=* : '.$this->keepOptionDescription().'}',
            $this->signature
        );

        parent::__construct();
    }

    public function getOutput()
    {
        return $this->output;
    }

    protected function configure(): void
    {
        parent::configure();

        $this->setDescription(__($this->descriptionKey()));
    }

    protected function keepOptionDescription(): string
    {
        return __('migration.keep');
    }

    /**
     * @return list<string>
     */
    protected function keptEmails(): array
    {
        $raw = $this->option('keep');
        if (! is_array($raw)) {
            $raw = ($raw === null || $raw === false || $raw === '') ? [] : [$raw];
        }

        $emails = [];
        foreach ($raw as $value) {
            foreach (explode(',', (string) $value) as $email) {
                $email = strtolower(trim($email));
                if ($email !== '') {
                    $emails[$email] = $email;
                }
            }
        }

        return array_values($emails);
    }

    /**
     * Emails to leave unchanged, or null when a full recreate was cancelled.
     *
     * @return list<string>|null
     */
    protected function confirmedKeepEmails(): ?array
    {
        $keep = $this->keptEmails();

        if ($keep !== [] || $this->confirm(__('migration.confirm_recreate'), false)) {
            return $keep;
        }

        $this->comment(__('migration.cancelled'));

        return null;
    }
}
