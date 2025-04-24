<?php

namespace App\Http\Controllers\Pivot\CashFlow;

use App\Http\Controllers\Controller;
use App\Models\CashFlow\PlanPayment;
use App\Models\CashFlow\PlanPaymentEntry;
use App\Models\CashFlow\PlanPaymentGroup;
use App\Models\Object\BObject;
use App\Models\Object\ReceivePlan;
use App\Services\PlanPaymentEntryService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PlanPaymentTableController extends Controller
{
    private PlanPaymentEntryService $planPaymentEntryService;

    public function __construct(PlanPaymentEntryService $planPaymentEntryService)
    {
        $this->planPaymentEntryService = $planPaymentEntryService;
    }

    public function index(Request $request): View
    {
        $planPaymentGroups = PlanPaymentGroup::all();
        $CFPlanPayments = PlanPayment::all();
        $CFPlanPaymentEntries = PlanPaymentEntry::all();
        $planGroupedPaymentTypes = $this->receivePlanService->getGroupedPlanPaymentTypes();
        $reasons = ReceivePlan::getReasons();
        $periods = $this->receivePlanService->getPeriods();
        $plans = $this->receivePlanService->getPlans(null, $periods[0]['start'], end($periods)['start']);

        $activeObjectIds = BObject::active()->orderBy('code')->pluck('id')->toArray();
        $closedObjectIds = ReceivePlan::whereBetween('date', [$periods[0]['start'], end($periods)['start']])->groupBy('object_id')->pluck('object_id')->toArray();

        $objects = BObject::whereIn('id', array_merge($activeObjectIds, $closedObjectIds))->get();

        return view(
            'pivots.cash-flow.index',
            compact(
                'reasons', 'periods', 'objects', 'plans',
                'planGroupedPaymentTypes', 'planPaymentGroups', 'CFPlanPayments', 'CFPlanPaymentEntries'
            )
        );
    }
}
