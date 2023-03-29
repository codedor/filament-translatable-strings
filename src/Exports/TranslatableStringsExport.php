<?php

namespace Codedor\TranslatableStrings\Exports;

use Codedor\TranslatableStrings\Exports\Sheets\TranslatableStringsPerScopeSheet;
use Codedor\TranslatableStrings\Models\TranslatableString;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;

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
