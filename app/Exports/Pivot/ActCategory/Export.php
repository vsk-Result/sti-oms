<?php

namespace App\Exports\Pivot\ActCategory;

use App\Exports\Pivot\ActCategory\Sheets\PivotSheet;
use App\Services\Contract\ActService;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class Export implements WithMultipleSheets
{
    private ActService $actService;

    public function __construct(ActService $actService)
    {
        $this->actService = $actService;
    }

    public function sheets(): array
    {
        return [
            new PivotSheet($this->actService),
        ];
    }
}
