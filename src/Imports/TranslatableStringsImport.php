<?php

namespace Codedor\TranslatableStrings\Imports;

use Codedor\TranslatableStrings\Models\TranslatableString;
use Maatwebsite\Excel\Concerns\SkipsUnknownSheets;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use Maatwebsite\Excel\Imports\HeadingRowFormatter;

class TranslatableStringsImport implements WithMultipleSheets, SkipsUnknownSheets
{
    public function sheets(): array
    {
        HeadingRowFormatter::default('none');

        return TranslatableString::groupedScopesWithoutFilament()->mapWithKeys(function ($title, $scope) {
            return [
                $title => new TranslatableStringScopeSheet($scope),
            ];
        })->toArray();
    }

    public function onUnknownSheet($sheetName)
    {
        // E.g. you can log that a sheet was not found.
        info("Sheet {$sheetName} was skipped");
    }
}
