<?php

namespace App\Http\Controllers\Pivot\CashFlow;

use App\Http\Controllers\Controller;
use App\Models\Object\BObject;
use App\Services\PlanPaymentService;
use App\Services\ReceivePlanService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PlanPaymentController extends Controller
{
    private PlanPaymentService $planPaymentService;
    private ReceivePlanService $receivePlanService;

    public function __construct(PlanPaymentService $planPaymentService, ReceivePlanService $receivePlanService)
    {
        $this->planPaymentService = $planPaymentService;
        $this->receivePlanService = $receivePlanService;
    }

    public function store(Request $request): JsonResponse
    {
        $payment = $this->planPaymentService->createPlanPayment($request->toArray());
        $periods = $this->receivePlanService->getPeriods();
        $objectList = BObject::active()->get();

        $gr = $request->get('group');
        $row = $request->has('group') ? 'pivots.cash-flow/partial/grouped_plan_payment_row' : 'pivots.cash-flow/partial/plan_payment_row';
        $view = view($row, compact('payment', 'periods', 'objectList', 'gr'))->render();

        $status = 'success';
        return response()->json(compact('status', 'view'));
    }

    public function update(Request $request): JsonResponse
    {
        $payment = $this->planPaymentService->updatePlanPayment($request->toArray());
        $periods = $this->receivePlanService->getPeriods();
        $objectList = BObject::active()->get();

        $gr = $request->get('group');
        $row = $request->has('group') ? 'pivots.cash-flow/partial/grouped_plan_payment_row' : 'pivots.cash-flow/partial/plan_payment_row';
        $view = view($row, compact('payment', 'periods', 'objectList', 'gr'))->render();

        $status = 'success';
        return response()->json(compact('status', 'view'));
    }

    public function destroy(Request $request): JsonResponse
    {
        $this->planPaymentService->destroyPlanPayment($request->toArray());

        $status = 'success';
        return response()->json(compact('status', ));
    }
}
