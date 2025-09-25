<?php

namespace App\Exports\Contract;

use App\Exports\Contract\Sheets\PivotSheet;
use App\Models\Object\BObject;
use Illuminate\Database\Eloquent\Collection;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class Export implements WithMultipleSheets
{
    private BObject $object;
    private Collection $contracts;
    private array $total;

    public function __construct(BObject $object, Collection $contracts, array $total)
    {
        $this->object = $object;
        $this->contracts = $contracts;
        $this->total = $total;
    }

    public function sheets(): array
    {
        return [
            new PivotSheet($this->object, $this->contracts, $this->total),
        ];
    }
}
