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
        $view = view('pivots.cash-flow/partial/plan_payment_row', compact('payment', 'periods', 'objectList'))->render();

        $status = 'success';
        return response()->json(compact('status', 'view'));
    }
}
