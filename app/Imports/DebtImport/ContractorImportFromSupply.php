<?php

namespace App\Imports\DebtImport;

use App\Imports\Sheets\SheetForArray;
use Maatwebsite\Excel\Concerns\SkipsUnknownSheets;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class ContractorImportFromSupply implements WithMultipleSheets, SkipsUnknownSheets
{
    public function sheets(): array
    {
        return [
            'ПОДРЯДЧИКИ' => new SheetForArray(),
        ];
    }

    public function onUnknownSheet($sheetName) {}
}
