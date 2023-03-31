<?php

use Codedor\LocaleCollection\Facades\LocaleCollection;
use Codedor\LocaleCollection\Locale;
use Codedor\TranslatableStrings\Filament\Resources\TranslatableStringResource\Pages\ListTranslatableStrings;
use Codedor\TranslatableStrings\Tests\Fixtures\Models\User;
use function Pest\Livewire\livewire;

beforeEach(function () {
    LocaleCollection::push(new Locale('en'))
        ->push(new Locale('nl'));

    $this->strings = [createTranslatableString()];

    $this->actingAs(User::factory()->create());
});

it('can list posts', function () {
    livewire(ListTranslatableStrings::class)
        ->assertCanSeeTableRecords($this->strings);
});

it('can sort on new strings');

it('can show only empty strings');

it('has an edit link');
