<?php

namespace App\Http\Controllers\Debt;

use App\Http\Controllers\Controller;
use App\Services\DebtManualService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class DebtManualController extends Controller
{
    private DebtManualService $debtManualService;

    public function __construct(DebtManualService $debtManualService)
    {
        $this->debtManualService = $debtManualService;
    }

    public function update(Request $request): RedirectResponse
    {
        $this->debtManualService->updateDebtManual($request->toArray());
        return redirect()->back();
    }
}
