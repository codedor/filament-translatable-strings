<?php

use Wotz\LocaleCollection\Facades\LocaleCollection;
use Wotz\LocaleCollection\Locale;
use Wotz\TranslatableStrings\Filament\Resources\TranslatableStringResource;
use Wotz\TranslatableStrings\Tests\Fixtures\Models\User;

beforeEach(function () {
    LocaleCollection::push(new Locale('en'))
        ->push(new Locale('nl'));

    createTranslatableString(value: [
        'nl' => 'Nederlandse waarde',
    ]);

    $this->actingAs(User::factory()->create());
});

it('has an index page', function () {
    $this->get(TranslatableStringResource::getUrl('index'))->assertSuccessful();
});

it('has only an index and edit action', function () {
    expect(TranslatableStringResource::getPages())
        ->toHaveCount(2)
        ->toHaveKeys(['index', 'edit']);
});
