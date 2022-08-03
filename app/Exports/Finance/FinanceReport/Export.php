<?php

namespace App\Exports\Finance\FinanceReport;

use App\Exports\Finance\FinanceReport\Sheets\PivotSheet;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class Export implements WithMultipleSheets
{
    private array $info;

    public function __construct(array $info)
    {
        $this->info = $info;
    }

    public function sheets(): array
    {
        return [
            new PivotSheet('Сводная', $this->info),
        ];
    }
}
