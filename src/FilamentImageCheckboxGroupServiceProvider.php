<?php

namespace Daikazu\FilamentImageCheckboxGroup;

use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class FilamentImageCheckboxGroupServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {

        $package
            ->name('filament-image-checkbox-group')
            ->hasConfigFile()
            ->hasViews();
    }
}
