<?php

use Illuminate\Support\Facades\Queue;
use Wotz\LocaleCollection\Facades\LocaleCollection;
use Wotz\LocaleCollection\Locale;
use Wotz\TranslatableStrings\Imports\Sheets\TranslatableStringScopeSheet;
use Wotz\TranslatableStrings\Jobs\ExportToLang;
use Wotz\TranslatableStrings\Models\TranslatableString;

beforeEach(function () {
    LocaleCollection::push(new Locale('en'))
        ->push(new Locale('nl'));

    $this->sheet = new TranslatableStringScopeSheet('scope');

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

    Queue::fake();
});

it('can update a translatable string', function () {
    $this->sheet->collection(collect([
        collect([
            'name' => 'name',
            'nl' => 'new nl value',
            'en' => 'new en value',
        ]),
    ]));

    Queue::assertPushed(ExportToLang::class);

    $this->assertDatabaseMissing(TranslatableString::class, [
        'name' => 'name',
        'value->en' => 'en value',
        'value->nl' => 'nl value',
    ]);

    $this->assertDatabaseHas(TranslatableString::class, [
        'name' => 'name',
        'value->en' => 'new en value',
        'value->nl' => 'new nl value',
    ]);
});

it('will skip if no name is given', function () {
    $this->sheet->collection(collect([
        collect([
            'nl' => 'new nl value',
            'en' => 'new en value',
        ]),
    ]));

    Queue::assertPushed(ExportToLang::class);

    $this->assertDatabaseMissing(TranslatableString::class, [
        'name' => 'name',
        'value->nl' => 'new nl value',
        'value->en' => 'new en value',
    ]);

    $this->assertDatabaseHas(TranslatableString::class, [
        'name' => 'name',
        'value->nl' => 'nl value',
        'value->en' => 'en value',
    ]);
});

it('will skip if a locale is missing', function () {
    $this->sheet->collection(collect([
        collect([
            'name' => 'name',
            'en' => 'new en value',
        ]),
    ]));

    Queue::assertPushed(ExportToLang::class);

    $this->assertDatabaseMissing(TranslatableString::class, [
        'name' => 'name',
        'value->nl' => 'nl value',
        'value->en' => 'en value',
    ]);

    $this->assertDatabaseHas(TranslatableString::class, [
        'name' => 'name',
        'value->en' => 'new en value',
        'value->nl' => 'nl value',
    ]);
});
