<?php

namespace App\Exports\Pivot\ActCategory;

use App\Exports\Pivot\ActCategory\Sheets\PivotSheet;
use App\Services\Contract\ActService;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class Export implements WithMultipleSheets
{
    public function __construct(private ActService $actService, private array $requestData) {}

    public function sheets(): array
    {
        return [
            new PivotSheet($this->actService, $this->requestData)
        ];
    }
}
