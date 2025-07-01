<?php

namespace App\Exports\Pivot\CalculateWorkersCost;

use App\Exports\Pivot\CalculateWorkersCost\Sheets\ObjectPivotSheet;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class ExportByObject implements WithMultipleSheets
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
        $sheets = [];

        foreach ($this->info['objects'] as $objectName => $info) {
            $sheets[] = new ObjectPivotSheet($objectName, $info, $this->year);
        }

        return $sheets;
    }
}
