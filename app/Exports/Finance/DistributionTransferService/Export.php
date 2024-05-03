<?php

namespace App\Exports\Finance\DistributionTransferService;

use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use App\Exports\Finance\DistributionTransferService\Sheets\PivotSheet;

class Export implements WithMultipleSheets
{
    public function sheets(): array
    {
        return [new PivotSheet('Распределение услуг по трансферу')];
    }
}
