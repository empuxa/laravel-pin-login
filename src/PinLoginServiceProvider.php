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
            ->hasViews()
            ->hasTranslations()
            ->hasRoute('web');

        $this->loadMigrationsFrom(__DIR__ . '/../database/migrations');
    }
}
