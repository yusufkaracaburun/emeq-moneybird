<?php

namespace Emeq\Moneybird\Tests;

use Emeq\Moneybird\MoneybirdServiceProvider;
use Illuminate\Database\Eloquent\Factories\Factory;
use Orchestra\Testbench\TestCase as Orchestra;

class TestCase extends Orchestra
{
    protected function setUp(): void
    {
        parent::setUp();

        Factory::guessFactoryNamesUsing(
            fn (string $modelName) => 'Emeq\\Moneybird\\Database\\Factories\\'.class_basename($modelName).'Factory'
        );
    }

    protected function tearDown(): void
    {
        if (class_exists(\Mockery::class)) {
            \Mockery::close();
        }

        if ($this->app !== null) {
            parent::tearDown();
        }
    }

    protected function getPackageProviders($app)
    {
        return [
            MoneybirdServiceProvider::class,
        ];
    }

    public function getEnvironmentSetUp($app)
    {
        config()->set('database.default', 'testing');

        $migration = include __DIR__.'/../database/migrations/create_moneybird_connections_table.php.stub';
        $migration->up();
    }
}
