<?php

namespace App\Http\Controllers\Debt;

use App\Http\Controllers\Controller;
use App\Services\DebtManualService;
use App\Services\PivotObjectDebtService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class DebtManualController extends Controller
{
    private DebtManualService $debtManualService;
    private PivotObjectDebtService $pivotObjectDebtService;

    public function __construct(DebtManualService $debtManualService, PivotObjectDebtService $pivotObjectDebtService)
    {
        $this->debtManualService = $debtManualService;
        $this->pivotObjectDebtService = $pivotObjectDebtService;
    }

    public function store(Request $request): RedirectResponse
    {
        $this->debtManualService->storeDebtManual($request->toArray());
        return redirect()->back();
    }

    public function update(Request $request): RedirectResponse
    {
        $this->debtManualService->updateDebtManual($request->toArray());
        $this->pivotObjectDebtService->updatePivotDebtForObject($request->get('debt_manual_object_id'));
        return redirect()->back();
    }
}
