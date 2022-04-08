<?php

namespace CodeviceCompany\LaravelHst\Actions\Support;

use CodeviceCompany\LaravelHst\DTOs\Support\InstallComposerPackagesDTO;
use Lorisleiva\Actions\Concerns\AsAction;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Process\PhpExecutableFinder;
use Symfony\Component\Process\Process;
use function base_path;

class InstallComposerPackagesAction
{
    use AsAction;

    private ?array $command;
    private ?OutputInterface $output;

    public function handle(InstallComposerPackagesDTO $dto)
    {
        $this->output = $dto->output;

        if ($dto->composer !== 'global') {
            $this->command = [$this->phpBinary(), $dto->composer, 'require'];
        }

        if ($dto->packages) {
            $this->installPackages($dto->packages);
        }

        if ($dto->devPackages) {
            $this->installDevPackages($dto->devPackages);
        }
    }

    /**
     * Get the path to the appropriate PHP binary.
     *
     * @return string
     */
    protected function phpBinary(): string
    {
        return (new PhpExecutableFinder())->find(false) ?: 'php';
    }

    private function installPackages(array|string $packages): void
    {
        $command = array_merge(
            $command ?? ['composer', 'require'],
            is_array($packages) ? $packages : [$packages]
        );

        (new Process($command, base_path(), ['COMPOSER_MEMORY_LIMIT' => '-1']))
            ->setTimeout(null)
            ->run(fn ($type, $output) => $this->output?->write($output));
    }

    private function installDevPackages(array|string $packages): void
    {
        $command = array_merge(
            $this->command ?? ['composer', 'require', '--dev'],
            is_array($packages) ? $packages : [$packages]
        );

        (new Process($command, base_path(), ['COMPOSER_MEMORY_LIMIT' => '-1']))
            ->setTimeout(null)
            ->run(fn ($type, $output) => $this->output?->write($output));
    }
}
