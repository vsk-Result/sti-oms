<?php

namespace App\Http\Controllers\Deposit;

use App\Http\Controllers\Controller;
use App\Http\Requests\Deposit\StoreDepositRequest;
use App\Http\Requests\Deposit\UpdateDepositRequest;
use App\Models\Bank;
use App\Models\Company;
use App\Models\Contract\Contract;
use App\Models\Currency;
use App\Models\Deposit;
use App\Models\Object\BObject;
use App\Models\Organization;
use App\Models\Status;
use App\Services\DepositService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DepositController extends Controller
{
    private DepositService $depositService;

    public function __construct(DepositService $depositService)
    {
        $this->depositService = $depositService;
    }

    public function index(Request $request): View
    {
        $total = [];
        $objects = BObject::orderBy('code')->get();

        if (auth()->user()->hasRole(['object-leader', 'finance-object-user'])) {
            $objects = BObject::whereIn('id', auth()->user()->objects->pluck('id'))->orderBy('code')->get();
        }

        $contracts = Contract::with('parent')->orderBy('name')->get();
        $deposits = $this->depositService->filterDeposit($request->toArray(), $total);
        $statuses = [
            Status::STATUS_ACTIVE => 'Активен',
            Status::STATUS_BLOCKED => 'В архиве',
            Status::STATUS_DELETED => 'Удален'
        ];

        return view('deposits.index', compact('statuses', 'deposits', 'contracts', 'objects', 'total'));
    }

    public function create(Request $request): View
    {
        $banks = Bank::getBanks();
        $objectId = $request->get('current_object_id') ?? null;
        $contractId = $request->get('current_contract_id') ?? null;
        $objects = BObject::orderBy('code')->get();
        $companies = Company::orderBy('id')->get();
        $organizations = Organization::orderBy('name')->get();
        $contracts = Contract::with('parent', 'object', 'children', 'children.object')->orderBy('name')->get();
        $currencies = Currency::getCurrencies();

        return view('deposits.create', compact('currencies', 'banks', 'objects', 'companies', 'objectId', 'organizations', 'contracts', 'contractId'));
    }

    public function store(StoreDepositRequest $request): RedirectResponse
    {
        $this->depositService->createDeposit($request->toArray());
        return redirect($request->get('return_url') ?? route('deposits.index'));
    }

    public function show(Deposit $deposit): View
    {
        return view('deposits.show', compact('deposit'));
    }

    public function edit(Deposit $deposit): View
    {
        $banks = Bank::getBanks();
        $objects = BObject::orderBy('code')->get();
        $companies = Company::orderBy('id')->get();
        $organizations = Organization::orderBy('name')->get();
        $contracts = Contract::with('parent', 'object', 'children', 'children.object')->orderBy('name')->get();
        $statuses = $deposit->getStatuses();
        $currencies = Currency::getCurrencies();
        return view('deposits.edit', compact('currencies', 'deposit', 'banks', 'objects', 'companies', 'statuses', 'organizations', 'contracts'));
    }

    public function update(Deposit $deposit, UpdateDepositRequest $request): RedirectResponse
    {
        $this->depositService->updateDeposit($deposit, $request->toArray());
        return redirect($request->get('return_url') ?? route('deposits.index'));
    }

    public function destroy(Deposit $deposit): RedirectResponse
    {
        $this->depositService->destroyDeposit($deposit);
        return redirect()->route('deposits.index');
    }
}
