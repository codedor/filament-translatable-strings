<?php

use Codedor\LocaleCollection\Facades\LocaleCollection;
use Codedor\LocaleCollection\Locale;
use Codedor\TranslatableStrings\Filament\Resources\TranslatableStringResource\Pages\EditTranslatableString;
use Codedor\TranslatableStrings\Tests\Fixtures\Models\User;
use function Pest\Livewire\livewire;

beforeEach(function () {
    LocaleCollection::push(new Locale('en'))
        ->push(new Locale('nl'));

    $this->string = createTranslatableString('scope', 'name', false, ['en' => 'en value', 'nl' => 'nl value']);

    $this->actingAs(User::factory()->create());
});

it('can edit a translatable string', function () {
    livewire(EditTranslatableString::class, [
        'record' => $this->string->getRouteKey(),
    ])
        ->assertSuccessful()
        ->assertFormSet([
            'scope' => $this->string->scope,
            'name' => $this->string->name,
            'value' => [
                'en' => 'en value',
                'nl' => 'nl value',
            ],
            'is_html' => $this->string->is_html,
        ])
        ->fillForm([
            'value' => [
                'en' => 'en new value',
                'nl' => 'nl value',
            ],
        ])
        ->call('save')
        ->assertHasNoFormErrors();

    expect($this->string->refresh())
        ->scope->toBe('scope')
        ->name->toBe('name')
        ->value->toBe('new en value')
        ->is_html->toBeFalsy();
})->todo('needs fix');
