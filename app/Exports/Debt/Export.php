<?php

namespace App\Exports\Debt;

use App\Exports\Debt\Sheets\ContractorSheet;
use App\Exports\Debt\Sheets\ContractorPivotSheet;
use App\Exports\Debt\Sheets\ProviderSheet;
use App\Exports\Debt\Sheets\ProviderPivotSheet;
use App\Exports\Debt\Sheets\ServiceSheet;
use App\Exports\Debt\Sheets\ServicePivotSheet;
use App\Models\Object\BObject;
use App\Services\PivotObjectDebtService;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class Export implements WithMultipleSheets
{
    private BObject $object;
    private PivotObjectDebtService $pivotObjectDebtService;

    public function __construct(BObject $object, PivotObjectDebtService $pivotObjectDebtService)
    {
        $this->object = $object;
        $this->pivotObjectDebtService = $pivotObjectDebtService;
    }

    public function sheets(): array
    {
        if ($this->object->code === '000') {
            return [
                new ContractorSheet($this->object, $this->pivotObjectDebtService),
                new ContractorPivotSheet($this->pivotObjectDebtService),
                new ProviderSheet($this->object, $this->pivotObjectDebtService),
                new ProviderPivotSheet($this->pivotObjectDebtService),
                new ServiceSheet($this->object, $this->pivotObjectDebtService),
                new ServicePivotSheet($this->pivotObjectDebtService),
            ];
        }

        return [
            new ContractorSheet($this->object, $this->pivotObjectDebtService),
            new ProviderSheet($this->object, $this->pivotObjectDebtService),
            new ServiceSheet($this->object, $this->pivotObjectDebtService),
        ];
    }
}
