<?php

namespace App\Http\Controllers\Company;

use App\Http\Controllers\Controller;
use App\Http\Requests\Company\StoreOrUpdateCompanyRequest;
use App\Models\Company;
use App\Models\Status;
use App\Services\CompanyService;
use Illuminate\Http\RedirectResponse;
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
