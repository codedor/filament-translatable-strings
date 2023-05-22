<?php

namespace Codedor\TranslatableStrings\Filament\Resources\TranslatableStringResource\Pages;

use Codedor\TranslatableStrings\Filament\Resources\TranslatableStringResource;
use Codedor\TranslatableTabs\Resources\Traits\HasTranslations;
use Filament\Resources\Pages\EditRecord;

class EditTranslatableString extends EditRecord
{
    use HasTranslations;

    protected static string $resource = TranslatableStringResource::class;
}
