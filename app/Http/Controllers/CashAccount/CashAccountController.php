<?php

namespace App\Http\Controllers\CashAccount;

use App\Http\Controllers\Controller;
use App\Models\CashAccount\CashAccount;
use App\Models\Object\BObject;
use App\Models\User;
use App\Services\CashAccount\CashAccountService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CashAccountController extends Controller
{
    public function __construct(private CashAccountService $cashAccountService) {}

    public function index(): View
    {
        $responsibleCashAccounts = $this->cashAccountService->getResponsibleCashAccounts();
        $sharedCashAccounts = $this->cashAccountService->getSharedCashAccounts();

        return view('cash-accounts.index', compact('responsibleCashAccounts', 'sharedCashAccounts'));
    }

    public function create(): View
    {
        $responsibleUsers = User::orderBy('name')->get();
        $sharedUsers = User::where('id', '!=', auth()->id())->orderBy('name')->get();
        $objects = BObject::getObjectCodes();

        if (!auth()->user()->hasRole('super-admin')) {
            $responsibleUsers = User::where('id', auth()->id())->get();
        }

        return view('cash-accounts.create', compact('responsibleUsers', 'objects', 'sharedUsers'));
    }

    public function store(Request $request): RedirectResponse
    {
        $this->cashAccountService->createCashAccount($request->toArray());
        return redirect()->route('cash_accounts.index');
    }

    public function show(CashAccount $cashAccount): RedirectResponse
    {
        return redirect()->route('cash_accounts.payments.index', $cashAccount);
    }

    public function edit(CashAccount $cashAccount): View
    {
        $responsibleUsers = User::orderBy('name')->get();
        $objects = BObject::getObjectCodes();
        $sharedUsers = User::where('id', '!=', $cashAccount->responsible_user_id)->orderBy('name')->get();

        if (!auth()->user()->hasRole('super-admin')) {
            $responsibleUsers = User::where('id', auth()->id())->get();
        }

        return view('cash-accounts.edit', compact('cashAccount', 'objects', 'sharedUsers', 'responsibleUsers'));
    }

    public function update(CashAccount $cashAccount, Request $request): RedirectResponse
    {
        $this->cashAccountService->updateCashAccount($cashAccount, $request->toArray());
        $this->cashAccountService->updateBalance($cashAccount);
        return redirect()->route('cash_accounts.index');
    }

    public function destroy(CashAccount $cashAccount): RedirectResponse
    {
        $this->cashAccountService->destroyCashAccount($cashAccount);
        return redirect()->route('cash_accounts.index');
    }
}
