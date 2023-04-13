<?php

namespace App\Imports\DebtImport;

use App\Imports\Sheets\SheetForArray;
use Maatwebsite\Excel\Concerns\SkipsUnknownSheets;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class Import implements WithMultipleSheets, SkipsUnknownSheets
{
    public function sheets(): array
    {
        return [
            'МАТЕРИАЛЫ' => new SheetForArray(),
            'ПОДРЯДЧИКИ' => new SheetForArray(),
            'Для фин отчёта' => new SheetForArray(),
            'ДТТЕРМО' => new SheetForArray(),
            'TDSheet' => new SheetForArray(),
            'ВЛАД СВОД' => new SheetForArray(),
            'DT' => new SheetForArray(),
        ];
    }

    public function onUnknownSheet($sheetName) {}
}
