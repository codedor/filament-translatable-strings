<?php

namespace Codedor\TranslatableStrings\Providers;

use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class TranslatableStringsServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        $package
            ->name('filament-translatable-strings')
            ->setBasePath(__DIR__ . '/../')
            ->hasConfigFile()
            ->hasMigration('2022_03_20_161514_create_translatable_strings_table')
            ->runsMigrations()
            ->hasTranslations();
    }
}
