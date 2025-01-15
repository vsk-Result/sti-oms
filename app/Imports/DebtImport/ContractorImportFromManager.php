<?php

namespace App\Imports\DebtImport;

use App\Imports\Sheets\SheetForArray;
use Maatwebsite\Excel\Concerns\SkipsUnknownSheets;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class ContractorImportFromManager implements WithMultipleSheets, SkipsUnknownSheets
{
    public function sheets(): array
    {
        return [
            'OMS' => new SheetForArray(),
            'Подрядчики' => new SheetForArray(),
            'СВОД' => new SheetForArray(),
            'ОтчетДДС (П)' => new SheetForArray(),
            'свод' => new SheetForArray(),
            'БАЗА Подрядчики' => new SheetForArray(),
            'Поставщики' => new SheetForArray(),
            'Свод по подряду' => new SheetForArray(),
        ];
    }

    public function onUnknownSheet($sheetName) {}
}
