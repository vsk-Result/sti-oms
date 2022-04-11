<?php

namespace App\Imports\Organization;

use App\Imports\Sheets\SheetForArray;
use Maatwebsite\Excel\Concerns\SkipsUnknownSheets;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class PaymentTransferImport implements WithMultipleSheets, SkipsUnknownSheets
{
    public function sheets(): array
    {
        return [
            'Организации' => new SheetForArray(),
        ];
    }

    public function onUnknownSheet($sheetName) {}
}
