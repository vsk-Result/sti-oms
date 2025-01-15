<?php

namespace App\Exports\Debt;

use App\Exports\Debt\Sheets\PivotSheet;
use App\Models\Object\BObject;
use App\Services\PivotObjectDebtService;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class Export implements WithMultipleSheets
{
    private BObject $object;
    private PivotObjectDebtService $pivotObjectDebtService;

    public function __construct(BObject $object, PivotObjectDebtService $pivotObjectDebtService)
    {
        $this->object = $object;
        $this->pivotObjectDebtService = $pivotObjectDebtService;
    }

    public function sheets(): array
    {
        return [
            new PivotSheet($this->object, $this->pivotObjectDebtService),
        ];
    }
}
