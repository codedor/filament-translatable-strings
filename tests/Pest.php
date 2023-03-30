<?php

use Codedor\LocaleCollection\Facades\LocaleCollection;
use Codedor\LocaleCollection\Locale;
use Codedor\TranslatableStrings\Models\TranslatableString;
use Codedor\TranslatableStrings\Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(TestCase::class, RefreshDatabase::class)->in('Feature');

function createTranslatableString(
    string $scope = 'scope',
    string $name = 'name',
    bool $isHtml = false,
    array $value = []
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
