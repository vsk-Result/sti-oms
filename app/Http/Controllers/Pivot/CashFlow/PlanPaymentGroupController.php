<?php

namespace App\Http\Controllers\Pivot\CashFlow;

use App\Http\Controllers\Controller;
use App\Models\CashFlow\PlanPaymentGroup;
use App\Services\PlanPaymentGroupService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class PlanPaymentGroupController extends Controller
{
    private PlanPaymentGroupService $planPaymentGroupService;

    public function __construct(PlanPaymentGroupService $planPaymentGroupService)
    {
        $this->planPaymentGroupService = $planPaymentGroupService;
    }

    public function store(Request $request): RedirectResponse
    {
        $this->planPaymentGroupService->createPlanPaymentGroup($request->toArray());
        return redirect()->back();
    }

    public function update(Request $request): RedirectResponse
    {
        $this->planPaymentGroupService->updatePlanPaymentGroup($request->toArray());
        return redirect()->back();
    }

    public function destroy(PlanPaymentGroup $group): RedirectResponse
    {
        $this->planPaymentGroupService->destroyPlanPaymentGroup(['group_id' => $group->id]);
        return redirect()->back();
    }
}
