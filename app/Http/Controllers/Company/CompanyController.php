<?php

namespace App\Http\Controllers\Company;

use App\Http\Controllers\Controller;
use App\Http\Requests\Company\StoreOrUpdateCompanyRequest;
use App\Models\Bank;
use App\Models\Company;
use App\Models\Organization;
use App\Models\Payment;
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

        $startCreditDate = Carbon::parse('2022-02-13');
        $credits = [
            [
                'bank_id' => 1,
                'bank' => Bank::getBankName(1),
                'contract' => '№ ВЛ/002020-006438 от 25.12.2020',
                'amount' => 100000000,
                'sent' => Payment::where('date', '>=', $startCreditDate)->where('date', '<=', $date)->where('description', 'LIKE', 'Погашение основного долга по договору № ВЛ/002020-006438 от 25.12.2020%')->sum('amount'),
                'received' => Payment::where('date', '>=', $startCreditDate)->where('date', '<=', $date)->where('description', 'LIKE', 'Выдача кредита по договору № ВЛ/002020-006438 от 25.12.2020%')->sum('amount')
            ]
        ];
        $totalCreditAmount = $credits[0]['amount'] - $credits[0]['amount'] - $credits[0]['received'];

        $DTOrganization = Organization::where('name', 'ООО "ДТ ТЕРМО ГРУПП"')->first();
        $PTIOrganization = Organization::where('name', 'ООО "ПРОМТЕХИНЖИНИРИНГ"')->first();

        $totalDebtDTAmount = - Payment::where('date', '>=', '2020-12-17')
            ->where('date', '<=', $date)
            ->where('type_id', Payment::TYPE_TRANSFER)
            ->where('payment_type_id', Payment::PAYMENT_TYPE_NON_CASH)
            ->where(function($q) use ($DTOrganization) {
                $q->where('organization_receiver_id', $DTOrganization->id);
                $q->orWhere('organization_sender_id', $DTOrganization->id);
            })
            ->sum('amount');

        $totalDebtPTIAmount = - Payment::where('date', '>=', '2021-02-12')
            ->where('date', '<=', $date)
            ->where('type_id', Payment::TYPE_TRANSFER)
            ->where('payment_type_id', Payment::PAYMENT_TYPE_NON_CASH)
            ->where('description', '!=', 'PATRIOT')
            ->where(function($q) {
                $q->where('bank_id', 1);
                $q->orWhere('bank_id', null);
            })
            ->where(function($q) use ($PTIOrganization) {
                $q->where('organization_receiver_id', $PTIOrganization->id);
                $q->orWhere('organization_sender_id', $PTIOrganization->id);
            })
            ->sum('amount');

        return view('companies.show', compact('company', 'balances', 'date', 'credits', 'totalCreditAmount', 'totalDebtDTAmount', 'totalDebtPTIAmount'));
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
