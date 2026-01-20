<?php

namespace Wotz\TranslatableStrings\Exports;

use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use Wotz\TranslatableStrings\Exports\Sheets\TranslatableStringsPerScopeSheet;
use Wotz\TranslatableStrings\Models\TranslatableString;

class TranslatableStringsExport implements WithMultipleSheets
{
    use Exportable;

    public function sheets(): array
    {
        return TranslatableString::groupedScopesWithoutFilament()
            ->map(fn (string $title, string $scope) => new TranslatableStringsPerScopeSheet($scope, $title))
            ->values()
            ->toArray();
    }
}
