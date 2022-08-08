<?php

namespace App\Exports\Finance\FinanceReport;

use App\Exports\Finance\FinanceReport\Sheets\ObjectPivotSheet;
use App\Exports\Finance\FinanceReport\Sheets\PivotSheet;
use App\Services\Contract\ContractService;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class Export implements WithMultipleSheets
{
    private array $info;

    private ContractService $contractService;

    public function __construct(array $info, ContractService $contractService)
    {
        $this->info = $info;
        $this->contractService = $contractService;
    }

    public function sheets(): array
    {
        return [
            new PivotSheet('Сводная по балансам и долгам', $this->info),
            new ObjectPivotSheet('Сводная по объектам', $this->contractService),
        ];
    }
}
