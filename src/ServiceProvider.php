<?php

namespace Empuxa\LoginViaPin;

use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class ServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        $package
            ->name('login-via-pin')
            ->hasConfigFile()
            ->hasViews()
            ->hasTranslations()
            ->hasRoute('web');

        $this->loadMigrationsFrom(__DIR__ . '/../database/migrations');
    }
}
