<?php

namespace App\Exports\Pivot\CalculateWorkersCost;

use App\Exports\Pivot\CalculateWorkersCost\Sheets\PivotSheet;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class ExportByCompany implements WithMultipleSheets
{
    private array $info;
    private $year;

    public function __construct(array $info, $year)
    {
        $this->info = $info;
        $this->year = $year;
    }

    public function sheets(): array
    {
        return [
            new PivotSheet($this->year, $this->info, $this->year),
        ];
    }
}
