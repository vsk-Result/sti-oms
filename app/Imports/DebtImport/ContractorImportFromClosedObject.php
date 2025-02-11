<?php

namespace App\Imports\DebtImport;

use App\Imports\Sheets\SheetForArray;
use Maatwebsite\Excel\Concerns\SkipsUnknownSheets;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class ContractorImportFromClosedObject implements WithMultipleSheets, SkipsUnknownSheets
{
    public function sheets(): array
    {
        return [
            'ГУ закрытые объекты' => new SheetForArray(),
        ];
    }

    public function onUnknownSheet($sheetName) {}
}
