<?php

namespace App\Imports\CostManager;

use App\Imports\Sheets\SheetForArray;
use Maatwebsite\Excel\Concerns\SkipsUnknownSheets;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class CostManagerImport implements WithMultipleSheets, SkipsUnknownSheets
{
    public function sheets(): array
    {
        return [
            'Лист1' => new SheetForArray(),
        ];
    }

    public function onUnknownSheet($sheetName) {}
}
