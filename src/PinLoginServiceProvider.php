<?php

namespace Empuxa\PinLogin;

use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class PinLoginServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        $package
            ->name('pin-login')
            ->hasConfigFile()
            ->hasMigration('add_pin_columns_to_users_table')
            ->hasTranslations()
            ->hasViews()
            ->hasRoute('web');

        // Fixes TestBench not being able to load and execute migrations.
        // Please create a PR if you know a better solution. Adding this to the
        // TestbenchTestCase.php file doesn't work for Laravel 9.
        $this->loadMigrationsFrom(__DIR__ . '/../database/migrations');
    }
}
