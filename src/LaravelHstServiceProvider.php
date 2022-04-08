<?php

namespace CodeviceCompany\LaravelHst;

use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;
use CodeviceCompany\LaravelHst\Commands\LaravelHstCommand;

class LaravelHstServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        /*
         * This class is a Package Service Provider
         *
         * More info: https://github.com/spatie/laravel-package-tools
         */
        $package
            ->name('laravel-hst')
            ->hasConfigFile()
            ->hasViews()
            ->hasMigration('create_laravel-hst_table')
            ->hasCommand(LaravelHstCommand::class);
    }
}
