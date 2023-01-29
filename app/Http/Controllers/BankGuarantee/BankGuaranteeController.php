<?php

namespace App\Http\Controllers\BankGuarantee;

use App\Http\Controllers\Controller;
use App\Http\Requests\BankGuarantee\StoreBankGuaranteeRequest;
use App\Http\Requests\BankGuarantee\UpdateBankGuaranteeRequest;
use App\Models\Bank;
use App\Models\BankGuarantee;
use App\Models\Company;
use App\Models\Contract\Contract;
use App\Models\Object\BObject;
use App\Models\Organization;
use App\Models\Status;
use App\Services\BankGuaranteeService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class BankGuaranteeController extends Controller
{
    private BankGuaranteeService $guaranteeService;

    public function __construct(BankGuaranteeService $guaranteeService)
    {
        $this->guaranteeService = $guaranteeService;
    }

    public function index(Request $request): View
    {
        $total = [];
        $objects = BObject::orderBy('code')->get();
        $contracts = Contract::with('parent')->orderBy('name')->get();
        $bankGuarantees = $this->guaranteeService->filterBankGuarantee($request->toArray(), $total);
        return view('bank-guarantees.index', compact('bankGuarantees', 'contracts', 'objects', 'total'));
    }

    public function create(Request $request): View
    {
        $banks = Bank::getBanks();
        $objectId = $request->get('current_object_id') ?? null;
        $contractId = $request->get('current_contract_id') ?? null;
        $objects = BObject::orderBy('code')->get();
        $companies = Company::orderBy('name')->get();
        $organizations = Organization::orderBy('name')->get();
        $contracts = Contract::with('parent', 'object', 'children', 'children.object')->orderBy('name')->get();
        $targets = BankGuarantee::getTargetsList();
        return view('bank-guarantees.create', compact('banks', 'objects', 'companies', 'targets', 'objectId', 'organizations', 'contracts', 'contractId'));
    }

    public function store(StoreBankGuaranteeRequest $request): RedirectResponse
    {
        $this->guaranteeService->createBankGuarantee($request->toArray());
        return redirect($request->get('return_url') ?? route('bank_guarantees.index'));
    }

    public function show(BankGuarantee $guarantee): View
    {
        return view('bank-guarantees.show', compact('guarantee'));
    }

    public function edit(BankGuarantee $guarantee): View
    {
        $banks = Bank::getBanks();
        $objects = BObject::orderBy('code')->get();
        $companies = Company::orderBy('name')->get();
        $targets = BankGuarantee::getTargetsList();
        $organizations = Organization::orderBy('name')->get();
        $contracts = Contract::with('parent', 'object', 'children', 'children.object')->orderBy('name')->get();
        $statuses = Status::getStatuses();
        return view('bank-guarantees.edit', compact('guarantee', 'banks', 'objects', 'companies', 'targets', 'statuses', 'organizations', 'contracts'));
    }

    public function update(BankGuarantee $guarantee, UpdateBankGuaranteeRequest $request): RedirectResponse
    {
        $this->guaranteeService->updateBankGuarantee($guarantee, $request->toArray());
        return redirect($request->get('return_url') ?? route('bank_guarantees.index'));
    }

    public function destroy(BankGuarantee $guarantee): RedirectResponse
    {
        $this->guaranteeService->destroyBankGuarantee($guarantee);
        return redirect()->route('bank_guarantees.index');
    }
}
