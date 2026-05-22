<?php

namespace App\Http\Controllers\Object;

use App\Exports\Debt\Export;
use App\Http\Controllers\Controller;
use App\Models\Object\BObject;
use App\Services\LoanService;
use App\Services\PivotObjectDebtService;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class DebtExportController extends Controller
{
    private PivotObjectDebtService $pivotObjectDebtService;
    private LoanService $loanService;

    public function __construct(PivotObjectDebtService $pivotObjectDebtService, LoanService $loanService)
    {
        $this->pivotObjectDebtService = $pivotObjectDebtService;
        $this->loanService = $loanService;
    }

    public function store(BObject $object): BinaryFileResponse
    {
        return Excel::download(new Export($object, $this->pivotObjectDebtService, $this->loanService), 'Экспорт долгов по объекту ' . $object->getName() . '.xlsx');
    }
}
