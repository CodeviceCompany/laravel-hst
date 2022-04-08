<?php

namespace CodeviceCompany\LaravelHst\Commands;

use Illuminate\Console\Command;

class LaravelHstCommand extends Command
{
    public $signature = 'laravel-hst';

    public $description = 'My command';

    public function handle(): int
    {
        $this->comment('All done');

        return self::SUCCESS;
    }
}
