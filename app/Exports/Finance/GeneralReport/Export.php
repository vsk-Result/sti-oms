<?php

namespace App\Exports\Finance\GeneralReport;

use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use App\Exports\Finance\GeneralReport\Sheets\PivotSheet;

class Export implements WithMultipleSheets
{
    private array $years;
    private array $items;

    public function __construct(array $years, array $items)
    {
        $this->years = $years;
        $this->items = $items;
    }

    public function sheets(): array
    {
        return [new PivotSheet('Сводная по общим затратам', $this->years, $this->items)];
    }
}
