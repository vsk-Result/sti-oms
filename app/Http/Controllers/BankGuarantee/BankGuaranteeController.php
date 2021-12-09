<?php

namespace App\Http\Controllers\BankGuarantee;

use App\Http\Controllers\Controller;
use App\Http\Requests\BankGuarantee\StoreBankGuaranteeRequest;
use App\Http\Requests\BankGuarantee\UpdateBankGuaranteeRequest;
use App\Models\Bank;
use App\Models\BankGuarantee;
use App\Models\Company;
use App\Models\Object\BObject;
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

    public function index(): View
    {
        $bankGuarantees = BankGuarantee::with('company', 'object')->get();
        return view('bank-guarantees.index', compact('bankGuarantees'));
    }

    public function create(Request $request): View
    {
        $banks = Bank::getBanks();
        $objectId = $request->get('current_object_id') ?? null;
        $objects = BObject::orderBy('code')->get();
        $companies = Company::orderBy('name')->get();
        $targets = BankGuarantee::getTargetsList();
        return view('bank-guarantees.create', compact('banks', 'objects', 'companies', 'targets', 'objectId'));
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
        $statuses = Status::getStatuses();
        return view('bank-guarantees.edit', compact('guarantee', 'banks', 'objects', 'companies', 'targets', 'statuses'));
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
