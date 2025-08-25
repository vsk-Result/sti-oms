<?php

namespace App\Exports\Pivot\CalculateWorkersCost;

use App\Exports\Pivot\CalculateWorkersCost\Sheets\ObjectPivotSheet;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class ExportByObject implements WithMultipleSheets
{
    private array $info;

    public function __construct(array $info)
    {
        $this->info = $info;
    }

    public function sheets(): array
    {
        $sheets = [];

        foreach ($this->info['objects'] as $objectName => $info) {
            $sheets[] = new ObjectPivotSheet($objectName, $info, $this->info['years']);
        }

        return $sheets;
    }
}
