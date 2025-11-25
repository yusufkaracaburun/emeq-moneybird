<?php

namespace Emeq\Moneybird;

use Emeq\Moneybird\Commands\ConnectCommand;
use Emeq\Moneybird\Commands\RefreshTokensCommand;
use Emeq\Moneybird\Commands\TestConnectionCommand;
use Emeq\Moneybird\Http\Controllers\WebhookController;
use Emeq\Moneybird\Services\MoneybirdService;
use Emeq\Moneybird\Services\OAuthService;
use Illuminate\Http\Resources\Json\JsonResource;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class MoneybirdServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        $package
            ->name('moneybird')
            ->hasConfigFile()
            ->hasMigration('create_moneybird_connections_table')
            ->hasCommands([
                ConnectCommand::class,
                TestConnectionCommand::class,
                RefreshTokensCommand::class,
            ]);
    }

    public function packageRegistered(): void
    {
        $this->app->singleton(MoneybirdService::class, function ($app) {
            return new MoneybirdService(
                $app->make(OAuthService::class)
            );
        });

        $this->app->singleton(OAuthService::class);
    }

    public function packageBooted(): void
    {
        JsonResource::withoutWrapping();

        $this->loadRoutes();
        $this->autoPublishAssets();
    }

    /**
     * Auto-publish package assets during installation.
     */
    protected function autoPublishAssets(): void
    {
        if (! $this->app->runningInConsole() || $this->app->runningUnitTests()) {
            return;
        }

        $configPath    = config_path('moneybird.php');
        $migrationPath = database_path('migrations');
        $routesDir     = base_path('routes/moneybird');
        $webRoutesPath = base_path('routes/moneybird/web.php');
        $apiRoutesPath = base_path('routes/moneybird/api.php');

        if (! file_exists($configPath) && is_dir(config_path())) {
            try {
                copy(__DIR__.'/../config/moneybird.php', $configPath);
            } catch (\Throwable $e) {
            }
        }

        $migrationExists = false;

        if (is_dir($migrationPath)) {
            $files           = glob($migrationPath.'/*_create_moneybird_connections_table.php');
            $migrationExists = ! empty($files);
        }

        if (! $migrationExists && is_dir($migrationPath)) {
            try {
                $migrationFileName = date('Y_m_d_His').'_create_moneybird_connections_table.php';
                $targetPath        = $migrationPath.'/'.$migrationFileName;

                $stubContent = file_get_contents(__DIR__.'/../database/migrations/create_moneybird_connections_table.php.stub');
                file_put_contents($targetPath, $stubContent);
            } catch (\Throwable $e) {
            }
        }

        if (is_dir(base_path('routes'))) {
            try {
                if (! is_dir($routesDir)) {
                    mkdir($routesDir, 0755, true);
                }

                if (! file_exists($webRoutesPath)) {
                    copy(__DIR__.'/../routes/web.php', $webRoutesPath);
                }

                if (! file_exists($apiRoutesPath)) {
                    copy(__DIR__.'/../routes/api.php', $apiRoutesPath);
                }
            } catch (\Throwable $e) {
            }
        }
    }

    /**
     * Load package routes.
     */
    protected function loadRoutes(): void
    {
        $appWebRoutesPath     = base_path('routes/moneybird/web.php');
        $packageWebRoutesPath = __DIR__.'/../routes/web.php';

        if (file_exists($appWebRoutesPath)) {
            $this->loadRoutesFrom($appWebRoutesPath);
        } elseif (file_exists($packageWebRoutesPath)) {
            $this->loadRoutesFrom($packageWebRoutesPath);
        }

        $appApiRoutesPath     = base_path('routes/moneybird/api.php');
        $packageApiRoutesPath = __DIR__.'/../routes/api.php';

        if (file_exists($appApiRoutesPath)) {
            $this->app['router']->prefix('api')->group(function () use ($appApiRoutesPath): void {
                $this->loadRoutesFrom($appApiRoutesPath);
            });
        } elseif (file_exists($packageApiRoutesPath)) {
            $this->app['router']->prefix('api')->group(function () use ($packageApiRoutesPath): void {
                $this->loadRoutesFrom($packageApiRoutesPath);
            });
        }

        $routePath = config('moneybird.webhook.route', '/moneybird/webhook');

        if (str_starts_with($routePath, '/')) {
            $routePath = ltrim($routePath, '/');
        }

        $this->app['router']->post($routePath, [WebhookController::class, 'handle'])
            ->name('moneybird.webhook');
    }
}
