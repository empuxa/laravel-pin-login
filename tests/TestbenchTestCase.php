<?php

namespace Empuxa\LoginViaPin\Tests;

use Empuxa\LoginViaPin\Models\User;
use Empuxa\LoginViaPin\ServiceProvider;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class TestbenchTestCase extends \Orchestra\Testbench\TestCase
{
    protected function getPackageProviders($app): array
    {
        return [
            ServiceProvider::class,
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

        $app['config']->set('login-via-pin.model', User::class);
    }

    protected function createUser(array $params = [])
    {
        return config('login-via-pin.model')::create(array_merge(
        // Default Laravel params
            [
                'name'              => fake()->name(),
                'email'             => fake()->unique()->safeEmail(),
                'email_verified_at' => now(),
                'password'          => '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', // password
                'remember_token'    => Str::random(10),
            ],
            // Default package params
            [
                config('login-via-pin.columns.pin')             => '$2y$10$DJDW1ZCcd.6iqtq/JdivDuWTUCDxVES/efzv1e61CKLhdIJPupzI6', // 123456,
                config('login-via-pin.columns.pin_valid_until') => null,
            ],
// Additional test params
            $params,
        ));
    }
}
