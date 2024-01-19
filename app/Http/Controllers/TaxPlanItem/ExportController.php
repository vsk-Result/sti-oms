<?php

namespace App\Http\Controllers\TaxPlanItem;

use App\Exports\TaxPlan\Export;
use App\Http\Controllers\Controller;
use App\Services\TaxPlanItemService;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class ExportController extends Controller
{
    private TaxPlanItemService $taxPlanItemService;

    public function __construct(TaxPlanItemService $taxPlanItemService)
    {
        $this->taxPlanItemService = $taxPlanItemService;
    }

    public function store(Request $request): BinaryFileResponse
    {
        $total = [];
        $items = $this->taxPlanItemService->filterTaxPlan($request->toArray(), $total, false);
        return Excel::download(new Export($items), 'Экспорт плана налогов к оплате.xlsx');
    }
}
