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
        $objects = BObject::orderBy('code')->get();
        $companies = Company::orderBy('name')->get();
        $items = $this->taxPlanItemService->filterTaxPlan($request->toArray(), $total);

        return view('tax-plan.index', compact('items', 'total', 'companies', 'objects'));
    }

    public function create(): View
    {
        $companies = Company::orderBy('name')->get();
        $objects = BObject::orderBy('code')->get();
        return view('tax-plan.create', compact('companies', 'objects'));
    }

    public function store(StoreTaxPlanItemRequest $request): RedirectResponse
    {
        $this->taxPlanItemService->createItem($request->toArray());
        return redirect()->route('tax_plan.index');
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
        return redirect()->route('tax_plan.index');
    }

    public function destroy(TaxPlanItem $item): RedirectResponse
    {
        $this->taxPlanItemService->destroyItem($item);
        return redirect()->route('tax_plan.index');
    }
}
