<?php

namespace App\Exports\Pivot\OrganizationDebt;

use App\Exports\Pivot\Debt\Sheets\PivotSheet;
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
            new PivotSheet('Долги контрагентов', $this->pivot),
        ];
    }
}
