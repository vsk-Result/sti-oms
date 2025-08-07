<?php

namespace App\Http\Controllers\CashAccount;

use App\Http\Controllers\Controller;
use App\Models\CashAccount\CashAccount;
use App\Models\CashAccount\CashAccountPayment;
use App\Services\CashAccount\CashAccountService;
use App\Services\CashAccount\TransferCashService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class TransferCashController extends Controller
{
    public function __construct(
        private TransferCashService $transferCashService,
        private CashAccountService $cashAccountService
    ) {}

    public function store(CashAccount $cashAccount, Request $request): RedirectResponse
    {
        $this->transferCashService->transferCash($cashAccount, $request->toArray());
        return redirect()->back();
    }

    public function update(CashAccount $cashAccount, CashAccountPayment $payment, Request $request): RedirectResponse
    {
        $this->transferCashService->updateTransfer($cashAccount, $payment, $request->toArray());
        $this->cashAccountService->updateBalance($cashAccount);

        return redirect()->back();
    }
}
