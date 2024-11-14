<?php

namespace App\Imports\TaxSplit;

use App\Imports\Sheets\SheetForArray;
use Maatwebsite\Excel\Concerns\SkipsUnknownSheets;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class TaxSplitImport implements WithMultipleSheets, SkipsUnknownSheets
{
    public function sheets(): array
    {
        return [
            'Лист_1' => new SheetForArray(),
        ];
    }

    public function onUnknownSheet($sheetName) {}
}
