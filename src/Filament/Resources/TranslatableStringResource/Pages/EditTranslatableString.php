<?php

namespace Codedor\TranslatableStrings\Filament\Resources\TranslatableStringResource\Pages;

use Codedor\TranslatableStrings\Filament\Resources\TranslatableStringResource;
use Filament\Pages\Actions;
use Filament\Resources\Pages\EditRecord;

class EditTranslatableString extends EditRecord
{
    use EditRecord\Concerns\Translatable;

    protected static string $resource = TranslatableStringResource::class;

    protected function getActions(): array
    {
        return [
            Actions\LocaleSwitcher::make(),
        ];
    }
}
