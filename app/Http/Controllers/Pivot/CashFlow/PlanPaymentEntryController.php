<?php

namespace App\Http\Controllers\Pivot\CashFlow;

use App\Http\Controllers\Controller;
use App\Models\Object\BObject;
use App\Services\PlanPaymentEntryService;
use App\Services\ReceivePlanService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PlanPaymentEntryController extends Controller
{
    private PlanPaymentEntryService $planPaymentEntryService;
    private ReceivePlanService $receivePlanService;

    public function __construct(PlanPaymentEntryService $planPaymentEntryService, ReceivePlanService $receivePlanService)
    {
        $this->planPaymentEntryService = $planPaymentEntryService;
        $this->receivePlanService = $receivePlanService;
    }

    public function store(Request $request): JsonResponse
    {
        $entry = $this->planPaymentEntryService->createOrUpdatePlanPaymentEntry($request->toArray());
        $payment = $entry->planPayment;
        $periods = $this->receivePlanService->getPeriods();
        $objectList = BObject::active()->get();

        $gr = $request->get('group');
        $row = $request->has('group') ? 'pivots.cash-flow/partial/grouped_plan_payment_row' : 'pivots.cash-flow/partial/plan_payment_row';
        $view = view($row, compact('payment', 'periods', 'objectList', 'gr'))->render();

        $status = 'success';
        return response()->json(compact('status', 'view'));
    }
}
