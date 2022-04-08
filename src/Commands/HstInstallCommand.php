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

//        $this->installComposerPackages();
//
//        $this->installNodePackages();
//
        $this->publishFiles();

        $this->line('');
        $this->comment('npm install && npm run dev && git config core.hooksPath ./githooks/ && chmod +x githooks/*');

        //render('<div class="px-1 bg-green-300">Successfully installed</div>');

        return self::SUCCESS;
    }

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
    }

    private function installComposerPackages(): void
    {
        InstallComposerPackagesAction::run(new InstallComposerPackagesDTO(
            packages: [
                "blade-ui-kit/blade-heroicons",
                "lorisleiva/laravel-actions",
                "owenvoke/blade-fontawesome",
                "spatie/data-transfer-object",
                "spatie/laravel-enum",
                "spatie/laravel-permission",
                "spatie/laravel-query-builder",
                "spatie/laravel-ray",
                "spatie/laravel-translatable",
                "spatie/laravel-view-models",
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
        tap(new Filesystem(), function (Filesystem $files) {
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

    /**
     * @return void
     */
    private function publishFiles(): void
    {
        if (! is_dir($stubsPath = $this->laravel->basePath())) {
            (new Filesystem())->makeDirectory($stubsPath);
        }

        $files = [
            __DIR__ . '/stubs/unpublishable/tailwind.config.js.stub' => $stubsPath . '/tailwind.config.js',
            __DIR__ . '/stubs/unpublishable/env.example.stub' => $stubsPath . '/.env.example',
            __DIR__ . '/stubs/unpublishable/env.testing.stub' => $stubsPath . '/.env.testing',
            __DIR__ . '/stubs/unpublishable/webpack.mix.js.stub' => $stubsPath . '/webpack.mix.js',
            __DIR__ . '/stubs/unpublishable/gitignore.stub' => $stubsPath . '/.gitignore',
            __DIR__ . '/stubs/unpublishable/wipit.stub' => $stubsPath . '/wipit',
            __DIR__ . '/stubs/unpublishable/phpstan.neon.stub' => $stubsPath . '/phpstan.neon',
            __DIR__ . '/stubs/unpublishable/phpmd.xml.stub' => $stubsPath . '/phpmd.xml',
            __DIR__ . '/stubs/unpublishable/php-cs-fixer.php.stub' => $stubsPath . '/.php-cs-fixer.php',

            __DIR__ . '/stubs/unpublishable/githooks/pre-push.stub' => $stubsPath . '/githooks/pre-push',
            __DIR__ . '/stubs/unpublishable/githooks/pre-commit.stub' => $stubsPath . '/githooks/pre-commit',

            __DIR__ . '/stubs/unpublishable/.github/test_and_deploy_main.yml.stub' => $stubsPath . '/.github/test_and_deploy_main.yml',
        ];

        if (! is_dir($path = $this->laravel->basePath('/githooks'))) {
            (new Filesystem())->makeDirectory($path, 0755, true);
        }

        if (! is_dir($path = $this->laravel->basePath('.github'))) {
            (new Filesystem())->makeDirectory($path, 0755, true);
        }

        foreach ($files as $from => $to) {
            file_put_contents($to, file_get_contents($from));
        }
    }
}
