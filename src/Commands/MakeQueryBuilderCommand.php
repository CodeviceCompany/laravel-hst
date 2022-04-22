<?php

namespace CodeviceCompany\LaravelHst\Commands;

use Illuminate\Console\GeneratorCommand;

class MakeQueryBuilderCommand extends GeneratorCommand
{
    protected $signature = 'hst:make:query-builder {name}';

    protected $description = 'Create a new query builder class';

    protected $type = 'QueryBuilder';

    protected function getStub(): string
    {
        return $this->resolveStubPath('/stubs/query-builder.stub');
    }

    protected function resolveStubPath($stub): string
    {
        return file_exists($customPath = $this->laravel->basePath(trim($stub, '/')))
            ? $customPath
            : __DIR__.$stub;
    }

    protected function getDefaultNamespace($rootNamespace): string
    {
        return $rootNamespace.'\QueryBuilders';
    }
}
