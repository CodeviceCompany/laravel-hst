<?php

namespace CodeviceCompany\LaravelHst\Commands;

use Illuminate\Console\Concerns\CreatesMatchingTest;
use Illuminate\Console\GeneratorCommand;
use Illuminate\Support\Str;
use Symfony\Component\Console\Input\InputOption;

class MakeActionCommand extends GeneratorCommand
{
    use CreatesMatchingTest;

    protected $name = 'make:action';

    protected $description = 'Create a new action class';

    public function handle()
    {
        parent::handle();

        if ($this->option('dto')) {
            $this->call('make:dto', [
                'name' => $this->getDtoClassName($this->argument('name')),
            ]);
        }
    }

    protected function buildClass($name)
    {
        $replace = [];

        if ($this->option('dto')) {
            $replace = $this->buildDtoReplacements();
        }

        return str_replace(
            array_keys($replace),
            array_values($replace),
            parent::buildClass($name)
        );
    }

    protected function buildDtoReplacements()
    {
        $dto = $this->qualifyDTO($this->argument('name'));

        return [
            '{{ namespacedDTO }}' => $dto,
            '{{namespacedDTO}}' => $dto,
            '{{ dto }}' => class_basename($dto),
            '{{dto}}' => class_basename($dto),
        ];
    }

    protected function qualifyDTO(string $name)
    {
        $name = ltrim($name, '\\/');

        $name = str_replace('/', '\\', $name);

        $name = $this->getDtoClassName($name);

        $rootNamespace = $this->rootNamespace();

        if (Str::startsWith($name, $rootNamespace)) {
            return $name;
        }

        return is_dir(app_path('DTOs'))
            ? $rootNamespace.'DTOs\\'.$name
            : $rootNamespace.$name;
    }

    private function getDtoClassName(string $name)
    {
        $name = str($name)->replaceLast('Action', 'DTO');
        if (! $name->endsWith('DTO')) {
            $name = $name->append('DTO');
        }

        return $name;
    }

    protected function getStub()
    {
        if ($this->option('dto')) {
            return $this->resolveStubPath('/stubs/action.dto.stub');
        }

        return $this->resolveStubPath('/stubs/action.stub');
    }

    protected function resolveStubPath($stub): string
    {
        return file_exists($customPath = $this->laravel->basePath(trim($stub, '/')))
            ? $customPath
            : __DIR__.$stub;
    }

    protected function getOptions()
    {
        return [
            ['dto', 'd', InputOption::VALUE_NONE, 'Create a new data transfer object for action.'],
        ];
    }
}
