<?php

use Codedor\LocaleCollection\Facades\LocaleCollection;
use Codedor\LocaleCollection\Locale;
use Codedor\TranslatableStrings\Exports\Sheets\TranslatableStringsPerScopeSheet;
use Codedor\TranslatableStrings\Models\TranslatableString;

beforeEach(function () {
    LocaleCollection::push(new Locale('en'))
        ->push(new Locale('nl'));

    $this->sheet = new TranslatableStringsPerScopeSheet('scope', 'Scope');

    $this->translatableString = TranslatableString::create([
        'name' => 'name',
        'key' => 'scope.name',
        'value' => [
            'nl' => 'nl value',
            'en' => 'en value',
        ],
        'scope' => 'scope',
        'is_html' => false,
    ]);
});

it('can map a translatable string', function () {
    expect($this->sheet)
        ->map($this->translatableString)->toBe([
            'name' => 'name',
            'en' => 'en value',
            'nl' => 'nl value',
        ]);
});

it('can query the model', function () {
    expect($this->sheet->query()->get())
        ->toHaveCount(1)
        ->first()->toArray()->toMatchArray([
            'name' => 'name',
            'value' => [
                'nl' => 'nl value',
                'en' => 'en value',
            ],
        ]);
});

it('has a title', function () {
    expect($this->sheet->title())
        ->toBe('Scope');
});

it('has headings', function () {
    expect($this->sheet->headings())
        ->toMatchArray([
            'name',
            'en',
            'nl',
        ]);
});
