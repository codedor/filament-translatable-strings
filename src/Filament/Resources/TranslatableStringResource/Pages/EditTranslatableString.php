<?php

namespace Wotz\TranslatableStrings\Filament\Resources\TranslatableStringResource\Pages;

use Filament\Resources\Pages\EditRecord;
use Wotz\TranslatableStrings\Filament\Resources\TranslatableStringResource;
use Wotz\TranslatableTabs\Resources\Traits\HasTranslations;

class EditTranslatableString extends EditRecord
{
    use HasTranslations;

    protected static string $resource = TranslatableStringResource::class;
}
