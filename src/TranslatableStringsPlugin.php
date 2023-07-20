<?php

namespace Codedor\TranslatableStrings;

use Codedor\TranslatableStrings\Filament\Resources\TranslatableStringResource;
use Filament\Contracts\Plugin;
use Filament\Panel;

class TranslatableStringsPlugin implements Plugin
{
    protected bool $hasTranslatableStringResource = true;

    public static function make(): static
    {
        return app(static::class);
    }

    public function getId(): string
    {
        return 'filament-transtable-strings';
    }

    public function register(Panel $panel): void
    {
        if ($this->hasTranslatableStringResource()) {
            $panel->resources([
                TranslatableStringResource::class,
            ]);
        }
    }

    public function boot(Panel $panel): void
    {

    }

    public function translatableStringResource(bool $condition = true): static
    {
        // This is the setter method, where the user's preference is
        // stored in a property on the plugin object.
        $this->hasTranslatableStringResource = $condition;

        // The plugin object is returned from the setter method to
        // allow fluent chaining of configuration options.
        return $this;
    }

    public function hasTranslatableStringResource(): bool
    {
        // This is the getter method, where the user's preference
        // is retrieved from the plugin property.
        return $this->hasTranslatableStringResource;
    }
}
