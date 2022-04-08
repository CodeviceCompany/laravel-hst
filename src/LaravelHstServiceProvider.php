<?php

namespace CodeviceCompany\LaravelHst;

use CodeviceCompany\LaravelHst\Commands\MakeActionCommand;
use CodeviceCompany\LaravelHst\Commands\MakeDtoCommand;
use CodeviceCompany\LaravelHst\Commands\MakeQueryBuilderCommand;
use CodeviceCompany\LaravelHst\Commands\StubPublishCommand;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;
use CodeviceCompany\LaravelHst\Commands\LaravelHstCommand;

class LaravelHstServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        /*
         * More info: https://github.com/spatie/laravel-package-tools
         */
        $package
            ->name('laravel-hst')
            ->hasConfigFile()
            ->hasViews()
            // ->hasMigration('create_laravel-hst_table')
            ->hasCommands([
                MakeActionCommand::class,
                MakeDtoCommand::class,
                MakeQueryBuilderCommand::class,
                StubPublishCommand::class,
            ]);
    }
}
