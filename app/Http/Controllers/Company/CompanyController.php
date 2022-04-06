<?php

namespace App\Http\Controllers\Company;

use App\Http\Controllers\Controller;
use App\Http\Requests\Company\StoreOrUpdateCompanyRequest;
use App\Models\Bank;
use App\Models\Company;
use App\Models\PaymentImport;
use App\Models\Status;
use App\Services\CompanyService;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CompanyController extends Controller
{
    private CompanyService $companyService;

    public function __construct(CompanyService $companyService)
    {
        $this->companyService = $companyService;
    }

    public function index(): View
    {
        $companies = Company::orderBy('id')->get();
        return view('companies.index', compact('companies'));
    }

    public function create(): View
    {
        return view('companies.create');
    }

    public function store(StoreOrUpdateCompanyRequest $request): RedirectResponse
    {
        $this->companyService->createCompany($request->toArray());
        return redirect()->route('companies.index');
    }

    public function show(Company $company, Request $request): View
    {
        $date = Carbon::parse($request->get('balance_date', now()));
        $balances = [];

        foreach (Bank::getBanks() as $bankId => $banks) {
            $balance = PaymentImport::where('company_id', $company->id)
                ->where('date', '<=', $date)
                ->where('bank_id', $bankId)
                ->orderBy('date', 'desc')
                ->first()
                ?->outgoing_balance;

            $balances[Bank::getBankName($bankId)] = $balance ?? 0;
        }

        return view('companies.show', compact('company', 'balances', 'date'));
    }

    public function edit(Company $company): View
    {
        $statuses = Status::getStatuses();
        return view('companies.edit', compact('company', 'statuses'));
    }

    public function update(Company $company, StoreOrUpdateCompanyRequest $request): RedirectResponse
    {
        $this->companyService->updateCompany($company, $request->toArray());
        return redirect()->route('companies.index');
    }

    public function destroy(Company $company): RedirectResponse
    {
        $this->companyService->destroyCompany($company);
        return redirect()->route('companies.index');
    }
}
