<?php

namespace Codedor\TranslatableStrings\Models;

use Codedor\LocaleCollection\Facades\LocaleCollection;
use Codedor\LocaleCollection\Locale;
use Codedor\TranslatableStrings\ExtractTranslatableStrings;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Spatie\Translatable\HasTranslations;

class TranslatableString extends Model
{
    use HasTranslations;

    protected $translatable = ['value'];

    protected $fillable = ['scope', 'name', 'key', 'is_html', 'value'];

    public function scopeByOneEmptyValue(Builder $query): void
    {
        $query->orWhere(
            fn ($query) => LocaleCollection::each(
                fn (Locale $locale) => $query->orWhereNull("value->{$locale->locale()}")
            )
        );
    }

    public function scopeByAllEmptyValues(Builder $query): void
    {
        $query->where(
            fn ($query) => LocaleCollection::each(
                fn (Locale $locale) => $query->whereNull("value->{$locale->locale()}")
            )
        );
    }

    public function scopeByFilledInValues(Builder $query): void
    {
        $query->where(
            fn ($query) => LocaleCollection::each(
                fn (Locale $locale) => $query->whereNotNull("value->{$locale->locale()}")
            )
        );
    }

    public function getCleanScopeAttribute(): string
    {
        $scope = $this->scope;

        if ($scope === ExtractTranslatableStrings::JSON_GROUP) {
            return 'Default';
        }

        if (Str::contains($scope, '/')) {
            return Str::of($scope)
                ->explode('/')
                ->reject(fn (string $part) => $part === 'vendor')
                ->map(fn (string $part) => Str::headline($part))
                ->implode(' > ');
        }

        return Str::headline($scope);
    }

    public static function groupedScopes(): Collection
    {
        return self::query()
            ->select('scope')
            ->distinct()
            ->orderBy('scope')
            ->get()
            ->mapWithKeys(fn (TranslatableString $string) => [$string->scope => $string->clean_scope]);
    }

    public static function groupedScopesWithoutFilament(): Collection
    {
        return self::query()
            ->select('scope')
            ->where('scope', 'not like', 'vendor/filament%')
            ->distinct()
            ->orderBy('scope')
            ->get()
            ->mapWithKeys(fn (TranslatableString $string) => [$string->scope => $string->clean_scope]);
    }
}
