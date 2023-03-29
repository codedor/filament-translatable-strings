<?php

use Codedor\LocaleCollection\Facades\LocaleCollection;
use Codedor\LocaleCollection\Locale;
use Codedor\TranslatableStrings\Exports\Sheets\TranslatableStringsPerScopeSheet;
use Codedor\TranslatableStrings\Exports\TranslatableStringsExport;
use Codedor\TranslatableStrings\Models\TranslatableString;

beforeEach(function () {
    LocaleCollection::push(new Locale('en'))
        ->push(new Locale('nl'));

    $this->export = new TranslatableStringsExport();

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
    expect($this->export->sheets())
        ->toHaveCount(1)
        ->sequence(
            fn ($sheet) => $sheet->toBeInstanceOf(TranslatableStringsPerScopeSheet::class),
        );
});
