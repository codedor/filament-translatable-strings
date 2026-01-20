<?php

use Wotz\LocaleCollection\Facades\LocaleCollection;
use Wotz\LocaleCollection\Locale;
use Wotz\TranslatableStrings\Imports\Sheets\TranslatableStringScopeSheet;
use Wotz\TranslatableStrings\Imports\TranslatableStringsImport;
use Wotz\TranslatableStrings\Models\TranslatableString;

beforeEach(function () {
    LocaleCollection::push(new Locale('en'))
        ->push(new Locale('nl'));

    $this->import = new TranslatableStringsImport;

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

it('has sheets for each scope', function () {
    expect($this->import->sheets())
        ->toHaveCount(1)
        ->sequence(
            fn ($sheet) => $sheet->toBeInstanceOf(TranslatableStringScopeSheet::class),
        );
});

it('will log unknown sheet', function () {
    expect($this->import->onUnknownSheet('unknown'))
        ->toBeNull();
});
