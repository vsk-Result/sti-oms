<?php

namespace App\Imports\Reports\ITRObjectSalary;

use App\Imports\Reports\ITRObjectSalary\Sheets\DataSheet;
use App\Imports\Reports\ITRObjectSalary\Sheets\EngineerSheet;
use App\Imports\Reports\ITRObjectSalary\Sheets\ProjectSheet;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class ITRObjectSalaryImport implements WithMultipleSheets
{
    public function sheets(): array
    {
        return [
            'Инженеры' => new EngineerSheet(),
            'Проекты' => new ProjectSheet(),
            'Данные' => new DataSheet(),
        ];
    }
}
