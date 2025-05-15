<?php

namespace App\Exports\Pivot\CalculateWorkersCost;

use App\Exports\Pivot\CalculateWorkersCost\Sheets\PivotSheet;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class Export implements WithMultipleSheets
{
    private array $info;

    public function __construct(array $info)
    {
        $this->info = $info;
    }

    public function sheets(): array
    {
        return [
            new PivotSheet('2024', $this->info),
        ];
    }
}
