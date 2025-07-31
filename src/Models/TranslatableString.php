<?php

namespace Codedor\TranslatableStrings\Models;

use Codedor\TranslatableStrings\ExtractTranslatableStrings;
use Codedor\TranslatableStrings\Jobs\ExportToLang;
use Codedor\TranslatableStrings\Models\Builders\TranslatableStringBuilder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Spatie\Translatable\HasTranslations;

/**
 * @property string $scope
 * @property string $key
 * @property string $name
 * @property bool $is_html
 * @property array $value
 */
class TranslatableString extends Model
{
    use HasTranslations;

    protected $table = 'translatable_strings';

    protected $translatable = ['value'];

    protected $fillable = ['scope', 'name', 'key', 'is_html', 'value'];

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

    public static function booted()
    {
        self::updated(fn (self $record) => ExportToLang::dispatch($record->scope));
    }

    public function newEloquentBuilder($query): TranslatableStringBuilder
    {
        return new TranslatableStringBuilder($query);
    }
}
