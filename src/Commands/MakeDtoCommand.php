<?php

namespace CodeviceCompany\LaravelHst\Commands;

use Illuminate\Console\Concerns\CreatesMatchingTest;
use Illuminate\Console\GeneratorCommand;

class MakeDtoCommand extends GeneratorCommand
{
    use CreatesMatchingTest;

    protected $name = 'hst:make:dto';

    protected $description = 'Create a new Data Transfer Object class';

    protected $type = 'DTO';

    protected function getStub(): string
    {
        return $this->resolveStubPath('/stubs/dto.stub');
    }

    protected function resolveStubPath($stub): string
    {
        return file_exists($customPath = $this->laravel->basePath(trim($stub, '/')))
            ? $customPath
            : __DIR__.$stub;
    }

    protected function getDefaultNamespace($rootNamespace): string
    {
        return $rootNamespace.'\DTOs';
    }
}
