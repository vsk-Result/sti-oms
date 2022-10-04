<?php

namespace App\Http\Controllers\Guarantee;

use App\Http\Controllers\Controller;
use App\Http\Requests\Guarantee\StoreGuaranteeRequest;
use App\Http\Requests\Guarantee\UpdateGuaranteeRequest;
use App\Models\Guarantee;
use App\Models\Company;
use App\Models\Contract\Contract;
use App\Models\Object\BObject;
use App\Models\Organization;
use App\Models\Status;
use App\Services\GuaranteeService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class GuaranteeController extends Controller
{
    private GuaranteeService $guaranteeService;

    public function __construct(GuaranteeService $guaranteeService)
    {
        $this->guaranteeService = $guaranteeService;
    }

    public function index(Request $request): View
    {
        $total = [];
        $objects = BObject::orderBy('code')->get();
        $contracts = Contract::with('parent')->orderBy('name')->get();
        $guarantees = $this->guaranteeService->filterGuarantee($request->toArray(), $total);
        return view('guarantees.index', compact('guarantees', 'contracts', 'objects', 'total'));
    }

    public function create(Request $request): View
    {
        $objectId = $request->get('current_object_id') ?? null;
        $objects = BObject::orderBy('code')->get();
        $companies = Company::orderBy('name')->get();
        $organizations = Organization::orderBy('name')->get();
        $contracts = Contract::with('parent')->orderBy('name')->get();
        return view('guarantees.create', compact('objects', 'companies', 'objectId', 'organizations', 'contracts'));
    }

    public function store(StoreGuaranteeRequest $request): RedirectResponse
    {
        $this->guaranteeService->createGuarantee($request->toArray());
        return redirect($request->get('return_url') ?? route('guarantees.index'));
    }

    public function show(Guarantee $guarantee): View
    {
        return view('guarantees.show', compact('guarantee'));
    }

    public function edit(Guarantee $guarantee): View
    {
        $objects = BObject::orderBy('code')->get();
        $companies = Company::orderBy('name')->get();
        $organizations = Organization::orderBy('name')->get();
        $contracts = Contract::with('parent')->orderBy('name')->get();
        $statuses = Status::getStatuses();
        return view('guarantees.edit', compact('guarantee', 'objects', 'companies', 'statuses', 'organizations', 'contracts'));
    }

    public function update(Guarantee $guarantee, UpdateGuaranteeRequest $request): RedirectResponse
    {
        $this->guaranteeService->updateGuarantee($guarantee, $request->toArray());
        return redirect($request->get('return_url') ?? route('guarantees.index'));
    }

    public function destroy(Guarantee $guarantee): RedirectResponse
    {
        $this->guaranteeService->destroyGuarantee($guarantee);
        return redirect()->route('guarantees.index');
    }
}
