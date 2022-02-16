<?php

namespace App\Imports\Contract;

use App\Imports\Sheets\SheetForArray;
use Maatwebsite\Excel\Concerns\SkipsUnknownSheets;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class ContractImport implements WithMultipleSheets, SkipsUnknownSheets
{
    public function sheets(): array
    {
        return [
            'Договора' => new SheetForArray(),
            'Авансы' => new SheetForArray(),
            'Полученные авансы' => new SheetForArray(),
            'Акты' => new SheetForArray(),
            'Банковские гарантии' => new SheetForArray()
        ];
    }

    public function onUnknownSheet($sheetName) {}
}
