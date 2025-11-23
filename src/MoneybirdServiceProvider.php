<?php

namespace Emeq\Moneybird;

use Emeq\Moneybird\Commands\ConnectCommand;
use Emeq\Moneybird\Commands\RefreshTokensCommand;
use Emeq\Moneybird\Commands\TestConnectionCommand;
use Emeq\Moneybird\Http\Controllers\WebhookController;
use Emeq\Moneybird\Services\MoneybirdService;
use Emeq\Moneybird\Services\OAuthService;
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
        $this->loadRoutes();
        $this->autoPublishAssets();
    }

    protected function autoPublishAssets(): void
    {
        // Only auto-publish during console commands (not web requests) and not during tests
        if (! $this->app->runningInConsole() || $this->app->runningUnitTests()) {
            return;
        }

        $configPath = config_path('moneybird.php');
        $migrationPath = database_path('migrations');
        $routesPath = base_path('routes/moneybird.php');

        // Auto-publish config if it doesn't exist
        if (! file_exists($configPath) && is_dir(config_path())) {
            try {
                copy(__DIR__.'/../config/moneybird.php', $configPath);
            } catch (\Throwable $e) {
                // Silently fail if we can't copy (e.g., permissions issue)
            }
        }

        // Auto-publish migration if it doesn't exist
        $migrationExists = false;
        if (is_dir($migrationPath)) {
            $files = glob($migrationPath.'/*_create_moneybird_connections_table.php');
            $migrationExists = ! empty($files);
        }

        if (! $migrationExists && is_dir($migrationPath)) {
            try {
                $migrationFileName = date('Y_m_d_His').'_create_moneybird_connections_table.php';
                $targetPath = $migrationPath.'/'.$migrationFileName;

                $stubContent = file_get_contents(__DIR__.'/../database/migrations/create_moneybird_connections_table.php.stub');
                file_put_contents($targetPath, $stubContent);
            } catch (\Throwable $e) {
                // Silently fail if we can't copy (e.g., permissions issue)
            }
        }

        // Auto-publish routes if it doesn't exist
        if (! file_exists($routesPath) && is_dir(base_path('routes'))) {
            try {
                copy(__DIR__.'/../routes/moneybird.php', $routesPath);
            } catch (\Throwable $e) {
                // Silently fail if we can't copy (e.g., permissions issue)
            }
        }
    }

    protected function loadRoutes(): void
    {
        // Routes are loaded from routes/moneybird.php if it exists (auto-published during installation)
        // Otherwise, load from package routes. The routes file should be included in routes/web.php
        // to ensure it gets the 'web' middleware group for session support.
        $appRoutesPath = base_path('routes/moneybird.php');
        $packageRoutesPath = __DIR__.'/../routes/moneybird.php';

        // Only load from package if app routes file doesn't exist
        // (app routes file should be included in routes/web.php)
        if (! file_exists($appRoutesPath) && file_exists($packageRoutesPath)) {
            $this->loadRoutesFrom($packageRoutesPath);
        }

        // Always register webhook route
        $routePath = config('moneybird.webhook.route', '/moneybird/webhook');

        if (str_starts_with($routePath, '/')) {
            $routePath = ltrim($routePath, '/');
        }

        $this->app['router']->post($routePath, [WebhookController::class, 'handle'])
            ->name('moneybird.webhook');
    }
}
