<?php

namespace App\Imports\DebtImport;

use App\Imports\Sheets\SheetForArray;
use Maatwebsite\Excel\Concerns\SkipsUnknownSheets;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class ProviderImportFromSupply implements WithMultipleSheets, SkipsUnknownSheets
{
    public function sheets(): array
    {
        return [
            'МАТЕРИАЛЫ' => new SheetForArray(),
        ];
    }

    public function onUnknownSheet($sheetName) {}
}
