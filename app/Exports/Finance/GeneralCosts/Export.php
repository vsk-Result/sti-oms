<?php

namespace App\Exports\Finance\GeneralCosts;

use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use App\Exports\Finance\GeneralCosts\Sheets\PivotSheet;

class Export implements WithMultipleSheets
{
    private array $requestYears;
    private array $requestObjects;
    private string $filterNDS;

    public function __construct(array $requestYears, array $requestObjects, string $filterNDS)
    {
        $this->requestYears = $requestYears;
        $this->requestObjects = $requestObjects;
        $this->filterNDS = $filterNDS;
    }

    public function sheets(): array
    {
        return [
            new PivotSheet('Общие затраты', $this->requestYears, $this->requestObjects, $this->filterNDS)
        ];
    }
}
