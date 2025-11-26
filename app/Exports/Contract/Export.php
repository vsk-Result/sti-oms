<?php

namespace App\Exports\Contract;

use App\Exports\Contract\Sheets\PivotSheet;
use App\Exports\Contract\Sheets\PivotSplitSheet;
use App\Models\Object\BObject;
use Illuminate\Database\Eloquent\Collection;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class Export implements WithMultipleSheets
{
    private BObject $object;
    private Collection $contracts;
    private array $total;
    private bool $splitContracts;

    public function __construct(BObject $object, Collection $contracts, array $total, bool $splitContracts)
    {
        $this->object = $object;
        $this->contracts = $contracts;
        $this->total = $total;
        $this->splitContracts = $splitContracts;
    }

    public function sheets(): array
    {
        if ($this->splitContracts) {
            return [
                new PivotSplitSheet($this->object, $this->contracts, $this->total),
            ];
        }

        return [
            new PivotSheet($this->object, $this->contracts, $this->total),
        ];
    }
}
