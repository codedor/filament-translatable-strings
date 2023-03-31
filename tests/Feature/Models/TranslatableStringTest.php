<?php

use Codedor\LocaleCollection\Facades\LocaleCollection;
use Codedor\LocaleCollection\Locale;
use Codedor\TranslatableStrings\ExtractTranslatableStrings;
use Codedor\TranslatableStrings\Models\TranslatableString;

beforeEach(function () {
    LocaleCollection::add(new Locale('nl'))
        ->add(new Locale('en'));

    $this->jsonString = createTranslatableString(
        value: [
            'nl' => null,
            'en' => 'en value',
        ],
        scope: ExtractTranslatableStrings::JSON_GROUP,
    );

    $this->packageString = createTranslatableString(
        value: [
            'nl' => 'nl value2',
            'en' => 'en value2',
        ],
        name: 'name2',
        scope: 'vendor/filament/form',
    );

    $this->groupString = createTranslatableString(
        value: [
            'nl' => null,
            'en' => null,
        ],
        name: 'name3',
        scope: 'group',
    );
});

it('can find strings where at least one translation is empty', function () {
    expect(TranslatableString::byOneEmptyValue()->get())
        ->toHaveCount(2)
        ->sequence(
            fn ($string) => $string->name->toBe('name'),
            fn ($string) => $string->name->toBe('name3'),
        );
});

it('can find strings where all translations are empty', function () {
    expect(TranslatableString::byAllEmptyValues()->get())
        ->toHaveCount(1)
        ->sequence(
            fn ($string) => $string->name->toBe('name3'),
        );
});

it('can find strings where all translations are filled in', function () {
    expect(TranslatableString::byFilledInValues()->get())
        ->toHaveCount(1)
        ->sequence(
            fn ($string) => $string->name->toBe('name2'),
        );
});

it('has a clean scope attribute for groups', function () {
    expect($this->groupString)
        ->clean_scope->toBe('Group');
});

it('has a clean scope attribute for JSON', function () {
    expect($this->jsonString)
        ->clean_scope->toBe('Default');
});

it('has a clean scope attribute for packages', function () {
    expect($this->packageString)
        ->clean_scope->toBe('Filament > Form');
});

it('can group scopes', function () {
    expect(TranslatableString::groupedScopes())
        ->toHaveCount(3)
        ->toArray()->toEqualCanonicalizing([
            ExtractTranslatableStrings::JSON_GROUP => 'Default',
            'vendor/filament/form' => 'Filament > Form',
            'group' => 'Group',
        ]);
});

it('can group scopes without filament', function () {
    expect(TranslatableString::groupedScopesWithoutFilament())
        ->toHaveCount(2)
        ->toArray()->toEqualCanonicalizing([
            ExtractTranslatableStrings::JSON_GROUP => 'Default',
            'group' => 'Group',
        ]);
});
