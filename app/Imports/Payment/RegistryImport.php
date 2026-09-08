<?php

namespace App\Imports\Payment;

use App\Imports\Sheets\SheetForArray;
use Maatwebsite\Excel\Concerns\SkipsUnknownSheets;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class RegistryImport implements WithMultipleSheets, SkipsUnknownSheets
{
    public function sheets(): array
    {
        return [
            'реестр' => new SheetForArray(),
        ];
    }

    public function onUnknownSheet($sheetName) {}
}
