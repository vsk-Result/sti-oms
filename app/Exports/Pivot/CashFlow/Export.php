<?php

namespace App\Exports\Pivot\CashFlow;

use App\Exports\Pivot\CashFlow\Sheets\PivotSheet;
use App\Exports\Pivot\CashFlow\Sheets\PivotSheetTwo;
use App\Services\FinanceReport\AccountBalanceService;
use App\Services\ReceivePlanService;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class Export implements WithMultipleSheets
{
    public function __construct(
        private ReceivePlanService $receivePlanService,
        private AccountBalanceService $accountBalanceService,
        private array $requestData
    ) {}

    public function sheets(): array
    {
        if ($this->requestData['view_name'] === 'view_two') {
            return [
                new PivotSheetTwo(
                    $this->receivePlanService,
                    $this->accountBalanceService,
                    $this->requestData
                ),
            ];
        }

        return [
            new PivotSheet(
                $this->receivePlanService,
                $this->accountBalanceService,
                $this->requestData
            ),
        ];
    }
}
