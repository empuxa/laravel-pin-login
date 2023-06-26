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
    }
}
