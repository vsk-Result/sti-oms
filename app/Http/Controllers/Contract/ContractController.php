<?php

namespace App\Http\Controllers\Contract;

use App\Http\Controllers\Controller;
use App\Models\Company;
use App\Models\Contract\Contract;
use App\Models\Object\BObject;
use App\Models\Status;
use App\Services\Contract\ActService;
use App\Services\Contract\ContractService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ContractController extends Controller
{
    private ContractService $contractService;
    private ActService $actService;

    public function __construct(ContractService $contractService, ActService $actService)
    {
        $this->contractService = $contractService;
        $this->actService = $actService;
    }

    public function index(Request $request): View
    {
        $total = [];
        $contracts = $this->contractService->filterContracts($request->toArray(), $total);
        $objects = BObject::orderBy('code')->get();

        $actsPaymentsLineChartInfo = $this->actService->prepareInfoForActsPaymentsLineChart($total['act_ids']);

        return view('contracts.index', compact('contracts', 'objects', 'total', 'actsPaymentsLineChartInfo'));
    }

    public function create(Request $request): View
    {
        $mainContracts = Contract::where('type_id', Contract::TYPE_MAIN)->pluck('name', 'id');
        $types = Contract::getTypes();
        $amountTypes = Contract::getAmountTypes();
        $objectId = $request->get('current_object_id') ?? null;
        $objects = BObject::orderBy('code')->get();
        $companies = Company::orderBy('name')->get();
        $currencies = ['RUB', 'EUR'];

        return view('contracts.create', compact('objects', 'companies', 'objectId', 'types', 'mainContracts', 'amountTypes', 'currencies'));
    }

    public function store(Request $request): RedirectResponse
    {
        $this->contractService->createContract($request->toArray());
        return redirect($request->get('return_url') ?? route('contracts.index'));
    }

    public function edit(Contract $contract): View
    {
        $mainContracts = Contract::where('type_id', Contract::TYPE_MAIN)->pluck('name', 'id');
        $types = Contract::getTypes();
        $amountTypes = Contract::getAmountTypes();
        $objects = BObject::orderBy('code')->get();
        $companies = Company::orderBy('name')->get();
        $statuses = Status::getStatuses();
        $currencies = ['RUB', 'EUR'];

        return view('contracts.edit', compact('contract', 'objects', 'companies', 'statuses', 'types', 'amountTypes', 'mainContracts', 'currencies'));
    }

    public function update(Contract $contract, Request $request): RedirectResponse
    {
        $this->contractService->updateContract($contract, $request->toArray());
        return redirect($request->get('return_url') ?? route('contracts.index'));
    }

    public function destroy(Contract $contract): RedirectResponse
    {
        $this->contractService->destroyContract($contract);
        return redirect()->back();
    }
}
