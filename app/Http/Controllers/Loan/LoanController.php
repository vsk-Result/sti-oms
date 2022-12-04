<?php

namespace App\Http\Controllers\Loan;

use App\Http\Controllers\Controller;
use App\Models\Bank;
use App\Models\Company;
use App\Models\Loan;
use App\Models\Organization;
use App\Models\Status;
use App\Services\LoanService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class LoanController extends Controller
{
    private LoanService $loanService;

    public function __construct(LoanService $loanService)
    {
        $this->loanService = $loanService;
    }

    public function index(Request $request): View
    {
        $total = [];
        $banks = Bank::getBanks();
        $types = Loan::getTypes();
        $organizations = Organization::orderBy('name')->get();
        $companies = Company::orderBy('name')->get();
        $loans = $this->loanService->filterLoans($request->toArray(), $total);
        return view('loans.index', compact('loans', 'types', 'companies', 'total', 'banks', 'organizations'));
    }

    public function create(): View
    {
        $banks = Bank::getBanks();
        $types = Loan::getTypes();
        $organizations = Organization::orderBy('name')->get();
        $companies = Company::orderBy('name')->get();
        return view('loans.create', compact('banks', 'types', 'companies', 'organizations'));
    }

    public function store(Request $request): RedirectResponse
    {
        $this->loanService->createLoan($request->toArray());
        return redirect($request->get('return_url') ?? route('loans.index'));
    }

    public function edit(Loan $loan): View
    {
        $banks = Bank::getBanks();
        $types = Loan::getTypes();
        $organizations = Organization::orderBy('name')->get();
        $companies = Company::orderBy('name')->get();
        $statuses = Status::getStatuses();
        return view('loans.edit', compact('loan', 'banks', 'types', 'companies', 'statuses', 'organizations'));
    }

    public function update(Loan $loan, Request $request): RedirectResponse
    {
        $this->loanService->updateLoan($loan, $request->toArray());
        return redirect($request->get('return_url') ?? route('loans.index'));
    }

    public function destroy(Loan $loan): RedirectResponse
    {
        $this->loanService->destroyLoan($loan);
        return redirect()->route('loans.index');
    }
}
