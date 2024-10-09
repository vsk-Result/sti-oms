<?php

namespace App\Imports\CRM\MoneyRegistry;

use App\Imports\Sheets\SheetForArray;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class Import implements WithMultipleSheets
{
    public function sheets(): array
    {
        return [
            'Лист_1' => new SheetForArray(),
        ];
    }
}
