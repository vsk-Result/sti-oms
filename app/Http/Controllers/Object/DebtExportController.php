<?php

namespace App\Http\Controllers\Object;

use App\Exports\Debt\Export;
use App\Http\Controllers\Controller;
use App\Models\Object\BObject;
use App\Services\PivotObjectDebtService;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class DebtExportController extends Controller
{
    private PivotObjectDebtService $pivotObjectDebtService;

    public function __construct(PivotObjectDebtService $pivotObjectDebtService)
    {
        $this->pivotObjectDebtService = $pivotObjectDebtService;
    }

    public function store(BObject $object): BinaryFileResponse
    {
        return Excel::download(new Export($object, $this->pivotObjectDebtService), 'Экспорт долгов по объекту ' . $object->getName() . '.xlsx');
    }
}
