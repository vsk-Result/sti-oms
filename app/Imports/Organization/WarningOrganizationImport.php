<?php

namespace App\Imports\Organization;

use App\Imports\Sheets\SheetForArray;
use Maatwebsite\Excel\Concerns\SkipsUnknownSheets;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class WarningOrganizationImport implements WithMultipleSheets, SkipsUnknownSheets
{
    public function sheets(): array
    {
        return [
            'судебные' => new SheetForArray(),
            'банкротные ' => new SheetForArray(),
            'претензии по дебиторке' => new SheetForArray(),
            'исп.листы' => new SheetForArray(),
        ];
    }

    public function onUnknownSheet($sheetName) {}
}
