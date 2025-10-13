<?php

namespace App\Exports\AccruedTax;

use App\Exports\AccruedTax\Sheets\PivotSheet;
use Illuminate\Database\Eloquent\Collection;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class Export implements WithMultipleSheets
{
    private array $names;
    private Collection $taxes;
    private array $dates;

    public function __construct(array $names, Collection $taxes, array $dates)
    {
        $this->names = $names;
        $this->taxes = $taxes;
        $this->dates = $dates;
    }

    public function sheets(): array
    {
        return [
            new PivotSheet($this->names, $this->taxes, $this->dates),
        ];
    }
}
