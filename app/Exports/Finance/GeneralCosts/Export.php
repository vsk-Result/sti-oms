<?php

namespace App\Exports\Finance\GeneralCosts;

use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use App\Exports\Finance\GeneralCosts\Sheets\PivotSheet;

class Export implements WithMultipleSheets
{
    public function sheets(): array
    {
        return [new PivotSheet('Общие затраты')];
    }
}
