<?php

namespace App\Http\Controllers\Object;

use App\Http\Controllers\Controller;
use App\Models\Object\BObject;
use App\Models\Object\CashFlowPayment;
use App\Models\Object\ReceivePlan;
use App\Services\ReceivePlanService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ReceivePlanController extends Controller
{
    private ReceivePlanService $receivePlanService;

    public function __construct(ReceivePlanService $receivePlanService)
    {
        $this->receivePlanService = $receivePlanService;
    }

    public function index(BObject $object): View
    {
        $reasons = ReceivePlan::getReasons();
        $periods = $this->receivePlanService->getPeriods($object->id);
        $plans = $this->receivePlanService->getPlans($object->id, $periods[0]['start'], end($periods)['start']);
        $cfPayments = $this->receivePlanService->getCFPayments($object->id, $periods);
        $cashFlowPayments = CashFlowPayment::where('object_id', $object->id)->get();

        return view(
            'objects.tabs.receive_plan',
            compact(
                'object', 'reasons', 'periods', 'plans', 'cfPayments', 'cashFlowPayments'
            )
        );
    }

    public function store(BObject $object, Request $request): JsonResponse
    {
        $requestDate = array_merge($request->toArray(), ['object_id' => $object->id]);
        $this->receivePlanService->updatePlan($requestDate);

        return response()->json(['status' => 'success', 'message' => 'Данные успешно обновлены!']);
    }
}
