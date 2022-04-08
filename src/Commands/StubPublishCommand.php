<?php

namespace CodeviceCompany\LaravelHst\Commands;

use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;

class StubPublishCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'hst:stub:publish {--force : Overwrite any existing files}';

    /**
     * The name of the console command.
     *
     * This name is used to identify the command during lazy loading.
     *
     * @var string|null
     *
     * @deprecated
     */
    protected static $defaultName = 'hst:stub:publish';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Publish all stubs that are available for customization';

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle()
    {
        if (! is_dir($stubsPath = $this->laravel->basePath('stubs'))) {
            (new Filesystem())->makeDirectory($stubsPath);
        }

        $files = [
            __DIR__.'/stubs/action.stub' => $stubsPath.'/action.stub',
            __DIR__.'/stubs/action.dto.stub' => $stubsPath.'/action.dto.stub',
            __DIR__.'/stubs/dto.stub' => $stubsPath.'/dto.stub',
            __DIR__.'/stubs/query-builder.stub' => $stubsPath.'/query-builder.stub',
        ];

        foreach ($files as $from => $to) {
            if (! file_exists($to) || $this->option('force')) {
                file_put_contents($to, file_get_contents($from));
            }
        }

        $this->info('Stubs published successfully.');
    }
}
