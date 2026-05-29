<?php

namespace App\Http\Controllers\Object;

use App\Http\Controllers\Controller;
use App\Models\Object\BObject;
use App\Models\PivotObjectDebt;
use App\Services\LoanService;
use App\Services\PivotObjectDebtService;
use Illuminate\Contracts\View\View;

class DebtController extends Controller
{
    public function __construct(private PivotObjectDebtService $pivotObjectDebtService, private LoanService $loanService) {}

    public function index(BObject $object): View
    {
        $contractorDebts = $this->pivotObjectDebtService->getPivotDebts($object->id, PivotObjectDebt::DEBT_TYPE_CONTRACTOR, ['with_sorted_details' => true]);
        $providerDebts = $this->pivotObjectDebtService->getPivotDebts($object->id, PivotObjectDebt::DEBT_TYPE_PROVIDER, ['with_sorted_details' => true]);
        $serviceDebts = $this->pivotObjectDebtService->getPivotDebts($object->id, PivotObjectDebt::DEBT_TYPE_SERVICE, ['with_sorted_details' => true]);
        $total = [];
        $loans = $this->loanService->filterLoans(['only_object_organizations' => true], $total);

        $hasExpiredManualUpload = $this->pivotObjectDebtService->hasExpiredManualPivots($object->id);

        return view('objects.tabs.debts', compact(
                'object', 'contractorDebts', 'providerDebts', 'serviceDebts',
                'hasExpiredManualUpload', 'total', 'loans'
            )
        );
    }
}
