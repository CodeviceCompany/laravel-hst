<?php

namespace CodeviceCompany\LaravelHst\Commands;

use CodeviceCompany\LaravelHst\Actions\Support\InstallComposerPackagesAction;
use CodeviceCompany\LaravelHst\DTOs\Support\InstallComposerPackagesDTO;
use Illuminate\Console\Command;
use Illuminate\Console\ConfirmableTrait;
use Illuminate\Filesystem\Filesystem;
use function Termwind\{render};

class HstInstallCommand extends Command
{
    use ConfirmableTrait;

    public $signature = 'hst:install  {--force : Overwrite any existing files}
                                       {--composer=global : Absolute path to the Composer binary which should be used to install packages}';

    public $description = 'Setup githooks/composer_dependencies/phpmd/phpstan/php-cs-fixer';


    public function handle(): int
    {
        if (! $this->confirmToProceed()) {
            return 1;
        }

        $this->installComposerPackages();

        $this->installNodePackages();

        //render('<div class="px-1 bg-green-300">Successfully installed</div>');

        return self::SUCCESS;
    }

    /**
     * @return void
     */
    private function installNodePackages(): void
    {
        $this->updateNodePackages(
            fn ($packages) => [
                    "@tailwindcss/aspect-ratio" => "^0.4.0",
                    "@tailwindcss/forms" => "^0.4.1",
                    "@tailwindcss/line-clamp" => "^0.3.0",
                    "@tailwindcss/typography" => "^0.5.2",
                    "alpinejs" => "^3.9.5",
                    "autoprefixer" => "^10.1.0",
                    "axios" => "^0.25",
                    "laravel-mix" => "^6.0.6",
                    "lodash" => "^4.17.19",
                    "postcss" => "^8.1.14",
                    "postcss-import" => "^14.0.1",
                    "tailwindcss" => "^3.0.23",
                ] + $packages
        );

        $this->line('');
        $this->info('NPM packages installed successfully.');
        $this->comment('Please execute "npm install && npm run dev" to build your assets.');
    }

    /**
     * @return void
     * @throws \Spatie\DataTransferObject\Exceptions\UnknownProperties
     */
    private function installComposerPackages(): void
    {
        InstallComposerPackagesAction::run(new InstallComposerPackagesDTO(
            packages: [
                "blade-ui-kit/blade-heroicons",
                "blade-ui-kit/blade-ui-kit",
                "lorisleiva/laravel-actions",
                "owenvoke/blade-fontawesome",
                "spatie/data-transfer-object",
                "spatie/laravel-enum",
                "spatie/laravel-permission",
                "spatie/laravel-query-builder",
                "spatie/laravel-ray",
                "spatie/laravel-translatable",
                "spatie/laravel-view-models",
                "squirephp/countries-en",
                "squirephp/countries-fr",
                "squirephp/model",
                "squirephp/timezones-en",
                "torann/geoip",
                "wire-elements/modal",
                "wireui/wireui",
            ],
            devPackages: [
                'barryvdh/laravel-debugbar',
                'friendsofphp/php-cs-fixer',
                'nunomaduro/larastan',
                'phpmd/phpmd',
                'squizlabs/php_codesniffer',
                'beyondcode/laravel-query-detector',
                'spatie/laravel-stubs',
                'spatie/laravel-web-tinker',
            ],
            output: $this->output,
            composer: $this->option('composer')
        ));
    }

    /**
     * Delete the "node_modules" directory and remove the associated lock files.
     *
     * @return void
     */
    protected static function flushNodeModules()
    {
        tap(new Filesystem, function (Filesystem $files) {
            $files->deleteDirectory(base_path('node_modules'));

            $files->delete(base_path('yarn.lock'));
            $files->delete(base_path('package-lock.json'));
        });
    }

    /**
     * Update the "package.json" file.
     *
     * @param  callable  $callback
     * @param  bool  $dev
     * @return void
     */
    protected static function updateNodePackages(callable $callback, bool $dev = true)
    {
        if (! file_exists(base_path('package.json'))) {
            return;
        }

        $configurationKey = $dev ? 'devDependencies' : 'dependencies';

        $packages = json_decode(file_get_contents(base_path('package.json')), true);

        $packages[$configurationKey] = $callback(
            array_key_exists($configurationKey, $packages) ? $packages[$configurationKey] : [],
            $configurationKey
        );

        ksort($packages[$configurationKey]);

        file_put_contents(
            base_path('package.json'),
            json_encode($packages, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT) . PHP_EOL
        );
    }
}
