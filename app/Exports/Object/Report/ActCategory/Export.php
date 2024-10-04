<?php

namespace App\Exports\Object\Report\ActCategory;

use App\Exports\Object\Report\ActCategory\Sheets\PivotSheet;
use App\Models\Object\BObject;
use App\Services\Contract\ActService;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class Export implements WithMultipleSheets
{
    private ActService $actService;
    private BObject $object;

    public function __construct(ActService $actService, BObject $object)
    {
        $this->actService = $actService;
        $this->object = $object;
    }

    public function sheets(): array
    {
        return [
            new PivotSheet($this->actService, $this->object),
        ];
    }
}
