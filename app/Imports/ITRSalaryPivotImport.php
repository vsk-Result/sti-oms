<?php

namespace App\Imports;

use App\Imports\Sheets\SheetForArray;
use Maatwebsite\Excel\Concerns\SkipsUnknownSheets;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class ITRSalaryPivotImport implements WithMultipleSheets, SkipsUnknownSheets
{
    public function sheets(): array
    {
        return [
            'История' => new SheetForArray(),
        ];
    }

    public function onUnknownSheet($sheetName) {}
}
