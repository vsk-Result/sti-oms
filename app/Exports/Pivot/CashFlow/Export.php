<?php

namespace App\Exports\Pivot\CashFlow;

use App\Exports\Pivot\CashFlow\Sheets\PivotSheet;
use App\Services\ReceivePlanService;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class Export implements WithMultipleSheets
{
    private ReceivePlanService $receivePlanService;

    public function __construct(ReceivePlanService $receivePlanService)
    {
        $this->receivePlanService = $receivePlanService;
    }

    public function sheets(): array
    {
        return [
            new PivotSheet($this->receivePlanService),
        ];
    }
}
