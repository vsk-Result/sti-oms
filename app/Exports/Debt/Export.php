<?php

namespace App\Exports\Debt;

use App\Exports\Debt\Sheets\DebtsSheet;
use App\Exports\Debt\Sheets\PivotSheet;
use App\Models\Object\BObject;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class Export implements WithMultipleSheets
{
    private BObject $object;

    public function __construct(BObject $object)
    {
        $this->object = $object;
    }

    public function sheets(): array
    {
        return [
            new PivotSheet($this->object),
            new DebtsSheet($this->object),
        ];
    }
}
