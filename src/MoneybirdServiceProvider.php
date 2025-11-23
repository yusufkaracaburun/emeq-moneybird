<?php

namespace Emeq\Moneybird;

use Emeq\Moneybird\Commands\ConnectCommand;
use Emeq\Moneybird\Commands\RefreshTokensCommand;
use Emeq\Moneybird\Commands\TestConnectionCommand;
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
        $this->app->singleton(\Emeq\Moneybird\Services\MoneybirdService::class, function ($app) {
            return new \Emeq\Moneybird\Services\MoneybirdService(
                $app->make(\Emeq\Moneybird\Services\OAuthService::class)
            );
        });

        $this->app->singleton(\Emeq\Moneybird\Services\OAuthService::class);
    }

    public function packageBooted(): void
    {
        $this->loadRoutes();
    }

    protected function loadRoutes(): void
    {
        $routePath = config('moneybird.webhook.route', '/moneybird/webhook');

        if (str_starts_with($routePath, '/')) {
            $routePath = ltrim($routePath, '/');
        }

        $this->app['router']->post($routePath, [\Emeq\Moneybird\Http\Controllers\WebhookController::class, 'handle'])
            ->name('moneybird.webhook');
    }
}
