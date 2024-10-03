<?php

namespace App\Http\Controllers\Pivot\CashFlow;

use App\Http\Controllers\Controller;
use App\Models\CashFlow\PlanPayment;
use App\Models\CashFlow\PlanPaymentEntry;
use App\Models\CashFlow\PlanPaymentGroup;
use App\Models\Object\BObject;
use App\Models\Object\ReceivePlan;
use App\Services\CashFlow\NotificationService;
use App\Services\ReceivePlanService;
use Illuminate\Contracts\View\View;

class CashFlowController extends Controller
{
    private ReceivePlanService $receivePlanService;
    private NotificationService $notificationService;

    public function __construct(ReceivePlanService $receivePlanService, NotificationService $notificationService)
    {
        $this->receivePlanService = $receivePlanService;
        $this->notificationService = $notificationService;
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

        $isNotificationsAvailable = in_array(auth()->id(), $this->notificationService->getTargetUserIds());
        $newNotifications = $this->notificationService->getNewNotifications();
        $historyNotifications = $this->notificationService->getHistoryNotifications();
        $hasUnreadNotifications = $this->notificationService->hasUnreadNotifications();

        return view(
            'pivots.cash-flow.index',
            compact(
                'periods', 'objects', 'plans',
               'planPaymentGroups', 'CFPlanPayments', 'CFPlanPaymentEntries', 'objectList', 'reasons',
                'hasUnreadNotifications', 'newNotifications', 'historyNotifications', 'isNotificationsAvailable'
            )
        );
    }
}
