<?php

namespace Empuxa\PinLogin\Tests;

use Empuxa\PinLogin\Models\User;
use Empuxa\PinLogin\PinLoginServiceProvider;
use Illuminate\Support\Str;

class TestbenchTestCase extends \Orchestra\Testbench\TestCase
{
    protected function getPackageProviders($app): array
    {
        return [
            PinLoginServiceProvider::class,
        ];
    }

    protected function defineDatabaseMigrations()
    {
        $this->loadLaravelMigrations();
    }

    protected function defineEnvironment($app)
    {
        // Setup default database to use sqlite :memory:
        $app['config']->set('database.default', 'testbench');
        $app['config']->set('database.connections.testbench', [
            'driver'   => 'sqlite',
            'database' => ':memory:',
            'prefix'   => '',
        ]);

        $app['config']->set('pin-login.model', User::class);
    }

    /**
     * @param array<int|string,mixed> $params
     */
    protected function createUser(array $params = [])
    {
        return config('pin-login.model')::create(array_merge(
            // Default Laravel params
            [
                'name'              => 'Test User',
                'email'             => 'user@example.com',
                'email_verified_at' => now(),
                'password'          => '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', // password
                'remember_token'    => Str::random(10),
            ],
            // Default package params
            [
                config('pin-login.columns.pin')             => '$2y$10$DJDW1ZCcd.6iqtq/JdivDuWTUCDxVES/efzv1e61CKLhdIJPupzI6', // 123456,
                config('pin-login.columns.pin_valid_until') => now()->addSecond(),
            ],
            // Additional test params
            $params,
        ));
    }
}
