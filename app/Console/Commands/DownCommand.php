<?php

namespace App\Console\Commands;

use Illuminate\Foundation\Console\DownCommand as BaseDownCommand;

class DownCommand extends BaseDownCommand
{
    /**
     * Prerender the dedicated Blade maintenance page unless another view was given.
     *
     * @return array<string, mixed>
     */
    protected function getDownFilePayload()
    {
        if (! $this->option('render')) {
            $this->input->setOption('render', 'errors.503');
        }

        return parent::getDownFilePayload();
    }
}
