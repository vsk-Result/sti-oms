<?php

namespace App\Imports\ManagerObject;

use App\Imports\Sheets\SheetForArray;
use Maatwebsite\Excel\Concerns\SkipsUnknownSheets;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class ManagerObjectImport implements WithMultipleSheets, SkipsUnknownSheets
{
    public function sheets(): array
    {
        return [
            'Лист1' => new SheetForArray(),
        ];
    }

    public function onUnknownSheet($sheetName) {}
}
