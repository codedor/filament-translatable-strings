<?php

use Illuminate\Foundation\Testing\RefreshDatabase;
use Wotz\LocaleCollection\Facades\LocaleCollection;
use Wotz\LocaleCollection\Locale;
use Wotz\TranslatableStrings\Models\TranslatableString;
use Wotz\TranslatableStrings\Tests\TestCase;

uses(TestCase::class, RefreshDatabase::class)->in('Feature');

function createTranslatableString(
    string $scope = 'scope',
    string $name = 'name',
    bool $isHtml = false,
    ?array $value = null,
): TranslatableString {
    return TranslatableString::create([
        'name' => $name,
        'scope' => $scope,
        'is_html' => $isHtml,
        'value' => $value ?: LocaleCollection::map(fn (Locale $locale) => [
            $locale->locale() => "{$locale->locale()} value",
        ])->toArray(),
        'key' => "{$scope}.{$name}",
    ]);
}
