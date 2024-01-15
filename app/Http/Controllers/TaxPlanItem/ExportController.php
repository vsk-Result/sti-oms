<?php

namespace App\Http\Controllers\TaxPlanItem;

use App\Exports\TaxPlan\Export;
use App\Http\Controllers\Controller;
use App\Models\TaxPlanItem;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class ExportController extends Controller
{
    public function store(): BinaryFileResponse
    {
        $items = TaxPlanItem::with('createdBy')->orderBy('due_date')->get();
        return Excel::download(new Export($items), 'Экспорт плана налогов к оплате.xlsx');
    }
}
