<?php

namespace App\Http\Controllers\TaxPlanItem;

use App\Http\Controllers\Controller;
use App\Models\Company;
use App\Models\Object\BObject;
use App\Models\TaxPlanItem;
use App\Services\TaxPlanItemService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use App\Http\Requests\TaxPlanItem\StoreTaxPlanItemRequest;
use App\Http\Requests\TaxPlanItem\UpdateTaxPlanItemRequest;
use Illuminate\Http\Request;

class TaxPlanItemController extends Controller
{
    private TaxPlanItemService $taxPlanItemService;

    public function __construct(TaxPlanItemService $taxPlanItemService)
    {
        $this->taxPlanItemService = $taxPlanItemService;
    }

    public function index(Request $request): View
    {
        $total = [];
        $names = TaxPlanItem::select('name')->distinct()->orderBy('name', 'ASC')->pluck('name')->toArray();
        $company_ids = TaxPlanItem::select('company_id')->distinct()->pluck('company_id')->toArray();
        $companies = Company::whereIn('id', $company_ids)->orderBy('name')->get();
        $object_ids = TaxPlanItem::select('object_id')->distinct()->pluck('object_id')->toArray();
        $objects = BObject::whereIn('id', $object_ids)->orderBy('code')->get();

        $items = $this->taxPlanItemService->filterTaxPlan($request->toArray(), $total);

        return view('tax-plan.index', compact('items', 'total', 'companies', 'objects', 'names'));
    }

    public function create(Request $request): View
    {
        $copyItem = null;

        if ($request->has('copy-item-id')) {
            $copyItem = TaxPlanItem::find($request->get('copy-item-id'));
        }

        $companies = Company::orderBy('name')->get();
        $objects = BObject::orderBy('code')->get();
        return view('tax-plan.create', compact('companies', 'objects', 'copyItem'));
    }

    public function store(StoreTaxPlanItemRequest $request): RedirectResponse
    {
        $this->taxPlanItemService->createItem($request->toArray());
        return redirect($request->get('return_url') ?? route('tax_plan.index', ['filter' => 'current']));
    }

    public function edit(TaxPlanItem $item): View
    {
        $companies = Company::orderBy('name')->get();
        $objects = BObject::orderBy('code')->get();
        return view('tax-plan.edit', compact('item', 'companies', 'objects'));
    }

    public function update(TaxPlanItem $item, UpdateTaxPlanItemRequest $request): RedirectResponse
    {
        $this->taxPlanItemService->updateItem($item, $request->toArray());
        return redirect($request->get('return_url') ?? route('tax_plan.index', ['filter' => 'current']));
    }

    public function destroy(TaxPlanItem $item): RedirectResponse
    {
        $this->taxPlanItemService->destroyItem($item);
        return redirect()->back();
    }
}
