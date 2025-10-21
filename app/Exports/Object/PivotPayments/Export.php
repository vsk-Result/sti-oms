<?php

namespace App\Exports\Object\PivotPayments;

use App\Models\Object\BObject;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use App\Exports\Object\PivotPayments\Sheets\PivotSheet;

class Export implements WithMultipleSheets
{
    public function __construct(
        private BObject $object,
        private array $services
    ) {}

    public function sheets(): array
    {
        return [
            new PivotSheet(
                $this->object,
                $this->services
            ),
        ];
    }
}
