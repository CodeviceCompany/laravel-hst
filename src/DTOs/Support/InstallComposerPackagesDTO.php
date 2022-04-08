<?php

namespace CodeviceCompany\LaravelHst\DTOs\Support;

use Spatie\DataTransferObject\DataTransferObject;
use Symfony\Component\Console\Output\OutputInterface;

class InstallComposerPackagesDTO extends DataTransferObject
{
    public array|string|null $packages;
    public array|string|null $devPackages;
    public ?OutputInterface $output;
    public string|array|bool|null $composer = null;
}
