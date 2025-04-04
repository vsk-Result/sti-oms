<?php

namespace App\Exports\Pivot\Residence;

use App\Exports\Pivot\Residence\Sheets\PivotSheet;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class Export implements WithMultipleSheets
{
    public function __construct(private array $residenceInfo, private string $date, private string $dormitoryName) {}

    public function sheets(): array
    {
        return [
            new PivotSheet($this->residenceInfo, $this->date, $this->dormitoryName),
        ];
    }
}
