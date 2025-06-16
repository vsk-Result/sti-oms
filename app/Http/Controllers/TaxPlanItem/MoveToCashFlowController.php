<?php

namespace App\Http\Controllers\TaxPlanItem;

use App\Http\Controllers\Controller;
use App\Services\PlanPaymentService;
use App\Services\ReceivePlanService;
use App\Services\TaxPlanItemService;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Cache;

class MoveToCashFlowController extends Controller
{
    public function __construct(
        private TaxPlanItemService $taxPlanItemService,
        private PlanPaymentService $planPaymentService,
        private ReceivePlanService $receivePlanService,
    ) {}

    public function store(): RedirectResponse
    {
        $periods = $this->receivePlanService->getPeriods();
        $payments = $this->taxPlanItemService->getPaymentsForCashFlow();

        $this->planPaymentService->updatePaymentsFromTaxPlan($payments, $periods);

        Cache::put('tax_plan_to_cash_flow_last_update', Carbon::now()->format('d.m.y H:i'));

        return redirect()->back();
    }
}
