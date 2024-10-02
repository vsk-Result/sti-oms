<?php

namespace App\Http\Controllers\Pivot\CashFlow;

use App\Http\Controllers\Controller;
use App\Models\CashFlow\PlanPayment;
use App\Models\CashFlow\PlanPaymentEntry;
use App\Models\CashFlow\PlanPaymentGroup;
use App\Models\Object\BObject;
use App\Models\Object\ReceivePlan;
use App\Services\ReceivePlanService;
use Illuminate\Contracts\View\View;

class CashFlowController extends Controller
{
    private ReceivePlanService $receivePlanService;

    public function __construct(ReceivePlanService $receivePlanService)
    {
        $this->receivePlanService = $receivePlanService;
    }

    public function index(): View
    {
        $reasons = ReceivePlan::getReasons();
        $planPaymentGroups = PlanPaymentGroup::all();
        $CFPlanPayments = PlanPayment::all();
        $CFPlanPaymentEntries = PlanPaymentEntry::all();
        $periods = $this->receivePlanService->getPeriods();
        $plans = $this->receivePlanService->getPlans(null, $periods[0]['start'], end($periods)['start']);

        $activeObjectIds = BObject::active()->orderBy('code')->pluck('id')->toArray();
        $closedObjectIds = ReceivePlan::whereBetween('date', [$periods[0]['start'], end($periods)['start']])->groupBy('object_id')->pluck('object_id')->toArray();

        $objects = BObject::whereIn('id', array_merge($activeObjectIds, $closedObjectIds))->get();
        $objectList = BObject::active()->get();

        return view(
            'pivots.cash-flow.index',
            compact(
                'periods', 'objects', 'plans',
               'planPaymentGroups', 'CFPlanPayments', 'CFPlanPaymentEntries', 'objectList', 'reasons'
            )
        );
    }
}
