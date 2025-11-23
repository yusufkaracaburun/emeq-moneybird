<?php

namespace Emeq\Moneybird\Tests;

use Emeq\Moneybird\MoneybirdServiceProvider;
use Illuminate\Database\Eloquent\Factories\Factory;
use Orchestra\Testbench\TestCase as Orchestra;

/**
 * @phpstan-template TApplication of \Illuminate\Foundation\Application
 *
 * @phpstan-extends Orchestra<TApplication>
 */
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

        try {
            if ($this->app !== null) {
                parent::tearDown();
            }
        } catch (\Throwable $e) {
            // Handle the case where HandleExceptions::flushState() is called with null test case
            // This can happen in CI environments with certain dependency versions
            $message = $e->getMessage();
            if (str_contains($message, 'HandleExceptions::flushState') ||
                str_contains($message, 'must be of type PHPUnit\\Framework\\TestCase') ||
                str_contains($message, 'Argument #1 ($test) must be of type')) {
                // Silently ignore this error as it's a known issue with Pest + Orchestra Testbench
                return;
            }
            throw $e;
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
        config()->set('database.connections.testing', [
            'driver' => 'sqlite',
            'database' => ':memory:',
            'prefix' => '',
            'foreign_key_constraints' => true,
        ]);

        $migration = include __DIR__.'/../database/migrations/create_moneybird_connections_table.php.stub';
        $migration->up();
    }
}
