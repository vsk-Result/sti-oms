<?php

namespace App\Exports\Debt;


use App\Exports\Debt\Sheets\AllDebtsContractorSheet;
use App\Exports\Debt\Sheets\AllDebtsProviderSheet;
use App\Exports\Debt\Sheets\AllDebtsServiceSheet;
use App\Services\PivotObjectDebtService;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class AllDebtsExport implements WithMultipleSheets
{
    private PivotObjectDebtService $pivotObjectDebtService;
    private array $object_ids;

    public function __construct(array $object_ids)
    {
        $this->pivotObjectDebtService = new PivotObjectDebtService();
        $this->object_ids = $object_ids;
    }

    public function sheets(): array
    //переделать свои sheets
    {
        return [
            new AllDebtsContractorSheet($this->object_ids, $this->pivotObjectDebtService),
            new AllDebtsProviderSheet($this->object_ids, $this->pivotObjectDebtService),
            new AllDebtsServiceSheet($this->object_ids, $this->pivotObjectDebtService),
        ];
    }
}