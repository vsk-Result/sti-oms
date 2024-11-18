<?php

namespace App\Exports\Pivot\CashFlow;

use App\Exports\Pivot\CashFlow\Sheets\PivotSheet;
use App\Services\ReceivePlanService;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class Export implements WithMultipleSheets
{
    public function __construct(private ReceivePlanService $receivePlanService, private array $requestData) {}

    public function sheets(): array
    {
        return [
            new PivotSheet($this->receivePlanService, $this->requestData),
        ];
    }
}
