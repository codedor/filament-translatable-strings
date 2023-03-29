<?php

namespace Codedor\TranslatableStrings\Imports\Sheets;

use Codedor\LocaleCollection\Facades\LocaleCollection;
use Codedor\LocaleCollection\Locale;
use Codedor\TranslatableStrings\Jobs\ExportToLang;
use Codedor\TranslatableStrings\Models\TranslatableString;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class TranslatableStringScopeSheet implements ToCollection, WithHeadingRow
{
    public function __construct(
        private string $scope
    ) {
    }

    public function collection(Collection $rows)
    {
        $rows->each(function (Collection $row) {
            if (! $row->has('name') || $row->get('name') === '') {
                return;
            }

            $string = TranslatableString::where('name', $row->get('name'))
                    ->where('scope', $this->scope)
                    ->first();

            LocaleCollection::each(function (Locale $locale) use ($row, &$string) {
                if (! $row->has($locale->locale())) {
                    return;
                }

                $string->setTranslation('value', $locale->locale(), $row->get($locale->locale()));
            });

            $string->save();
        });

        ExportToLang::dispatch($this->scope);
    }
}
