<?php

use Wotz\LocaleCollection\Facades\LocaleCollection;
use Wotz\LocaleCollection\Locale;
use Wotz\TranslatableStrings\Exports\Sheets\TranslatableStringsPerScopeSheet;
use Wotz\TranslatableStrings\Exports\TranslatableStringsExport;
use Wotz\TranslatableStrings\Models\TranslatableString;

beforeEach(function () {
    LocaleCollection::push(new Locale('en'))
        ->push(new Locale('nl'));

    $this->export = new TranslatableStringsExport;

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
