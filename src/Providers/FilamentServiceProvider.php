<?php

namespace Codedor\TranslatableStrings\Providers;

use Codedor\TranslatableStrings\Filament\Resources\TranslatableStringResource;
use Filament\PluginServiceProvider;
use Spatie\LaravelPackageTools\Package;

class FilamentServiceProvider extends PluginServiceProvider
{
    protected array $resources = [
        TranslatableStringResource::class,
    ];

    public function configurePackage(Package $package): void
    {
        $package->name('filament-translatable-strings');
    }
}
