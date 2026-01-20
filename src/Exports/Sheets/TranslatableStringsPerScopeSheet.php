<?php

namespace Wotz\TranslatableStrings\Exports\Sheets;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithTitle;
use Wotz\LocaleCollection\Facades\LocaleCollection;
use Wotz\LocaleCollection\Locale;
use Wotz\TranslatableStrings\Models\TranslatableString;

class TranslatableStringsPerScopeSheet implements FromQuery, ShouldAutoSize, WithHeadings, WithMapping, WithTitle
{
    private Collection $locales;

    public function __construct(
        private string $scope,
        private string $title
    ) {
        $this->locales = LocaleCollection::map(fn (Locale $locale) => $locale->locale());
    }

    public function map($translatableString): array
    {
        return $this->locales
            ->mapWithKeys(fn (string $locale) => [
                $locale => $translatableString->getTranslation('value', $locale, false),
            ])
            ->prepend($translatableString->name, 'name')
            ->toArray();
    }

    public function query(): Builder
    {
        return TranslatableString::select('name', 'value')->where('scope', $this->scope);
    }

    public function title(): string
    {
        return $this->title;
    }

    public function headings(): array
    {
        return array_merge(
            ['name'],
            $this->locales->toArray()
        );
    }
}
