<?php

namespace App\Http\Controllers\Pivot\CashFlow;

use App\Http\Controllers\Controller;
use App\Models\CashFlow\PlanPayment;
use App\Models\CashFlow\PlanPaymentEntry;
use App\Models\CashFlow\PlanPaymentGroup;
use App\Models\Object\BObject;
use App\Models\Object\ReceivePlan;
use App\Services\CashFlow\NotificationService;
use App\Services\FinanceReport\AccountBalanceService;
use App\Services\PlanPaymentService;
use App\Services\ReceivePlanService;
use Carbon\Carbon;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

class CashFlowController extends Controller
{
    public function __construct(
        private ReceivePlanService $receivePlanService,
        private NotificationService $notificationService,
        private PlanPaymentService $planPaymentService,
        private AccountBalanceService $accountBalanceService
    ) {}

    public function index(Request $request): View
    {
        $reasons = ReceivePlan::getReasons();
        $planPaymentGroups = PlanPaymentGroup::all();
        $CFPlanPayments = PlanPayment::all();
        $otherPlanPayments = PlanPayment::getOther();
        $CFPlanPaymentEntries = PlanPaymentEntry::all();
        $periods = $this->receivePlanService->getPeriods(null, $request->get('period'));
        $plans = $this->receivePlanService->getPlans(null, $periods[0]['start'], end($periods)['start']);

        $activeObjectIds = BObject::active()->orderBy('code')->pluck('id')->toArray();
        $closedObjectIds = ReceivePlan::whereBetween('date', [$periods[0]['start'], end($periods)['start']])->groupBy('object_id')->pluck('object_id')->toArray();

        $objects = BObject::whereIn('id', array_merge($activeObjectIds, $closedObjectIds))->get();
        $objectList = BObject::active()->get();

        $isNotificationsAvailable = in_array(auth()->id(), $this->notificationService->getTargetUserIds());
        $newNotifications = $this->notificationService->getNewNotifications();
        $historyNotifications = $this->notificationService->getHistoryNotifications();
        $hasUnreadNotifications = $this->notificationService->hasUnreadNotifications();

        $period = Carbon::parse($periods[0]['start'])->format('d.m.Y') . ' - ' . Carbon::parse(end($periods)['end'])->format('d.m.Y');

        $cfPayments = $this->receivePlanService->getCFPaymentsForAll($periods);

        $aho = PlanPayment::where('name', 'АХО')->first();

        if ($aho) {
            $this->planPaymentService->destroyPlanPayment(['payment_id' => $aho->id]);
        }

        $accounts = $this->accountBalanceService->getCurrentAccounts();

        $viewName = $request->get('view_name');
        $view = 'pivots.cash-flow.index';

        if ($viewName === 'view_two') {
            $view = 'pivots.cash-flow.index_two';
        }

        return view(
            $view,
            compact(
                'periods', 'objects', 'plans', 'period',
               'planPaymentGroups', 'CFPlanPayments', 'CFPlanPaymentEntries', 'objectList', 'reasons',
                'hasUnreadNotifications', 'newNotifications', 'historyNotifications', 'isNotificationsAvailable', 'otherPlanPayments',
                'cfPayments', 'viewName', 'accounts'
            )
        );
    }
}
