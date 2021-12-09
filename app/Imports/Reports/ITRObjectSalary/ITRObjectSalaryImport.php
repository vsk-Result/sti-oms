<?php

namespace App\Imports\Reports\ITRObjectSalary;

use App\Imports\Sheets\SheetForArray;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class ITRObjectSalaryImport implements WithMultipleSheets
{
    public function sheets(): array
    {
        return [
            'Инженеры' => new SheetForArray(),
            'Проекты' => new SheetForArray(),
            'Данные' => new SheetForArray(),
        ];
    }
}
