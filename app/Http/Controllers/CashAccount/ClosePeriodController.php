<?php

namespace App\Http\Controllers\CashAccount;

use App\Http\Controllers\Controller;
use App\Models\CashAccount\CashAccount;
use App\Models\CashAccount\ClosePeriod;
use App\Services\CashAccount\ClosePeriodService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class ClosePeriodController extends Controller
{
    public function __construct(private ClosePeriodService $closePeriodService) {}

    public function store(CashAccount $cashAccount, Request $request): RedirectResponse
    {
        $this->closePeriodService->createClosePeriod($cashAccount, $request->toArray());
        return redirect()->back();
    }

    public function update(CashAccount $cashAccount, ClosePeriod $closePeriod): RedirectResponse
    {
        $this->closePeriodService->updateClosePeriod($closePeriod);
        return redirect()->back();
    }
}
