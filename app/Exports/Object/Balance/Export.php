<?php

namespace App\Exports\Object\Balance;

use App\Exports\Object\Balance\Sheets\AnalyticsSheet;
use App\Models\Object\BObject;
use App\Services\PivotObjectDebtService;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use App\Exports\Object\Balance\Sheets\BalanceSheet;

class Export  implements WithMultipleSheets
{
    private BObject $object;
    private PivotObjectDebtService $pivotObjectDebtService;

    public function __construct(
        BObject $object,
        PivotObjectDebtService $pivotObjectDebtService,
    ) {
        $this->object = $object;
        $this->pivotObjectDebtService = $pivotObjectDebtService;
    }

    public function sheets(): array
    {
        return [
            new BalanceSheet(
                $this->object,
                $this->pivotObjectDebtService,
            ),
            new AnalyticsSheet($this->object),
        ];
    }
}
