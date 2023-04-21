<?php

namespace App\Exports\Pivot\DTSTI;

use App\Exports\Pivot\DTSTI\Sheets\PivotSheet;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class Export implements WithMultipleSheets
{
    private array $pivot;

    public function __construct(array $pivot)
    {
        $this->pivot = $pivot;
    }

    public function sheets(): array
    {
        return [
            new PivotSheet('Сводная', $this->pivot),
        ];
    }
}
