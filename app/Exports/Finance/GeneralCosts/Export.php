<?php

namespace App\Exports\Finance\GeneralCosts;

use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use App\Exports\Finance\GeneralCosts\Sheets\PivotSheet;

class Export implements WithMultipleSheets
{
    private array $requestYears;
    private array $requestObjects;

    public function __construct(array $requestYears, array $requestObjects)
    {
        $this->requestYears = $requestYears;
        $this->requestObjects = $requestObjects;
    }

    public function sheets(): array
    {
        return [new PivotSheet('Общие затраты', $this->requestYears, $this->requestObjects)];
    }
}
