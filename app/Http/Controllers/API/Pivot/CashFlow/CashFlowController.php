<?php

namespace App\Http\Controllers\API\Pivot\CashFlow;

use App\Http\Controllers\Controller;
use App\Models\CashFlow\PlanPayment;
use App\Models\CashFlow\PlanPaymentEntry;
use App\Models\CashFlow\PlanPaymentGroup;
use App\Models\Object\BObject;
use App\Models\Object\ReceivePlan;
use App\Services\ReceivePlanService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CashFlowController extends Controller
{
    public function __construct(private ReceivePlanService $receivePlanService) {}

    public function index(Request $request): JsonResponse
    {
        if (! $request->has('verify_hash')) {
            abort(403);
            return response()->json([], 403);
        }

        if ($request->get('verify_hash') !== config('qr.verify_hash')) {
            abort(403);
            return response()->json([], 403);
        }

        $periods = $this->receivePlanService->getPeriods();
        $periods = array_slice($periods, 0, 6);

        $planPaymentGroups = PlanPaymentGroup::all();
        $CFPlanPayments = PlanPayment::all();
        $otherPlanPayments = PlanPayment::getOther();
        $CFPlanPaymentEntries = PlanPaymentEntry::all();
        $reasons = ReceivePlan::getReasons();
        $plans = $this->receivePlanService->getPlans(null, $periods[0]['start'], end($periods)['start']);

        $activeObjectIds = BObject::active()->orderBy('code')->pluck('id')->toArray();
        $closedObjectIds = ReceivePlan::whereBetween('date', [$periods[0]['start'], end($periods)['start']])->groupBy('object_id')->pluck('object_id')->toArray();

        $objects = BObject::whereIn('id', array_merge($activeObjectIds, $closedObjectIds))->get();

        $receiveTotal = [
            'total_amount' => 0,
            'periods' => [],
            'receives' => [
                [
                    'name' => 'Целевые авансы',
                    'total_amount' => 0,
                    'periods' => []
                ],
                [
                    'name' => 'Прочие поступления',
                    'total_amount' => 0,
                    'periods' => []
                ]
            ]
        ];

        foreach($periods as $period) {
            $amount = $plans->where('date', $period['start'])->sum('amount');
            $targetAvansTotal = $plans->where('date', $period['start'])->where('reason_id', ReceivePlan::REASON_TARGET_AVANS)->sum('amount');
            $otherTotal = $plans->where('date', $period['start'])->where('reason_id', '!=', ReceivePlan::REASON_TARGET_AVANS)->sum('amount');

            $receiveTotal['total_amount'] += $amount;
            $receiveTotal['receives'][0]['total_amount'] += $targetAvansTotal;
            $receiveTotal['receives'][1]['total_amount'] += $otherTotal;

            $receiveTotal['periods'][] = array_merge($period, ['amount' => $amount]);
            $receiveTotal['receives'][0]['periods'][] = array_merge($period, ['amount' => $targetAvansTotal]);
            $receiveTotal['receives'][1]['periods'][] = array_merge($period, ['amount' => $otherTotal]);
        }

        $objectsInfo = [];

        foreach($objects as $object) {

            $totalAmount = 0;
            foreach($periods as $period) {
                $totalAmount += $plans->where('object_id', $object->id)->where('date', $period['start'])->sum('amount');
            }

            if ($totalAmount == 0) {
                continue;
            }

            $objectInfo = [
                'name' => $object->name,
                'code' => $object->code,
                'total_amount' => $totalAmount,
                'periods' => [],
                'reasons' => []
            ];

            foreach($periods as $period) {
                $amount = $plans->where('object_id', $object->id)->where('date', $period['start'])->sum('amount');
                $objectInfo['periods'][] = array_merge($period, ['amount' => $amount]);
            }

            foreach($reasons as $reasonId => $reason) {
                $totalAmount = $plans->where('object_id', $object->id)->where('reason_id', $reasonId)->sum('amount');

                if ($totalAmount == 0) {
                    continue;
                }

                $reasonInfo = [
                    'reason' => $reason,
                    'total_amount' => 0,
                    'periods' => [],
                ];

                $totalAmount = 0;
                foreach($periods as $period) {
                    $plan = $plans->where('object_id', $object->id)->where('date', $period['start'])->where('reason_id', $reasonId)->first();
                    $amount = 0;

                    if ($plan) {
                        $amount = $plan->amount;
                    }

                    $totalAmount += $amount;

                    $reasonInfo['periods'][] = array_merge($period, ['amount' => $amount]);
                }

                $reasonInfo['total_amount'] = $totalAmount;

                $objectInfo['reasons'][] = $reasonInfo;
            }

            $objectsInfo[] = $objectInfo;
        }

        $paymentsTotalInfo = [
            'total_amount' => 0,
            'periods' => [],
        ];
        $paymentsInfo = [];

        $totalAmount = 0;
        foreach($periods as $index => $period) {
            if ($index === 0) {
                $amount = $CFPlanPaymentEntries->where('date', '<=', $period['end'])->sum('amount') + array_sum($otherPlanPayments);
            } else {
                $amount = $CFPlanPaymentEntries->whereBetween('date', [$period['start'], $period['end']])->sum('amount');
            }

            $totalAmount += $amount;

            $paymentsTotalInfo['periods'][] = array_merge($period, ['amount' => $amount]);
        }

        $paymentsTotalInfo['total_amount'] = $totalAmount;

        $planGroupedPaymentAmount = [];
        foreach ($planPaymentGroups as $group) {
            if ($group->payments->count() === 0) {
                continue;
            }
            $planGroupedPaymentAmount[$group->name] = [];

            foreach ($group->payments as $payment) {
                foreach($periods as $index => $period) {
                    if ($index === 0) {
                        $amount = $payment->entries->where('date', '<=', $period['end'])->sum('amount');
                    } else {
                        $amount = $payment->entries->whereBetween('date', [$period['start'], $period['end']])->sum('amount');
                    }

                    if (! isset($planGroupedPaymentAmount[$group->name][$period['id']])) {
                        $planGroupedPaymentAmount[$group->name][$period['id']] = 0;
                    }
                    $planGroupedPaymentAmount[$group->name][$period['id']] += $amount;
                }
            }
        }

        $paymentsInfo['groups'] = [];

        foreach($planPaymentGroups as $group) {
            if ($group->payments->count() === 0) {
                continue;
            }

            $groupInfo = [
                'name' => $group->name,
                'object_code' => $group->object->code ?? '',
                'total_amount' => 0,
                'periods' => [],
                'payments' => []
            ];

            $groupTotal = 0;
            foreach($periods as $period) {
                $amount = $planGroupedPaymentAmount[$group->name][$period['id']];
                $groupTotal += $amount;

                $groupInfo['periods'][] = array_merge($period, ['amount' => $amount]);
            }

            $groupInfo['total_amount'] = $groupTotal;

            foreach($group->payments as $payment) {
                $totalAmount = $payment->entries->where('date', '<=', last($periods)['end'])->sum('amount');

                if (!is_valid_amount_in_range($totalAmount)) {
                    continue;
                }

                $paymentInfo = [
                    'name' => $payment->name,
                    'object_code' => $payment->object->code ?? '',
                    'total_amount' => $totalAmount,
                    'periods' => []
                ];

                foreach($periods as $index => $period) {
                    if ($index === 0) {
                        $amount = $payment->entries->where('date', '<=', $period['end'])->sum('amount');
                    } else {
                        $amount = $payment->entries->whereBetween('date', [$period['start'], $period['end']])->sum('amount');
                    }

                    $paymentInfo['periods'][] = array_merge($period, ['amount' => $amount]);
                }

                $groupInfo['payments'][] = $paymentInfo;
            }

            $paymentsInfo['groups'][] = $groupInfo;
        }

        $paymentsInfo['cf_payments'] = [];

        foreach($CFPlanPayments as $payment) {
            if (!is_null($payment->group_id)) {
                continue;
            }

            $totalAmount = $payment->entries->where('date', '<=', last($periods)['end'])->sum('amount');

            if (!is_valid_amount_in_range($totalAmount)) {
                continue;
            }

            $paymentInfo = [
                'name' => $payment->name,
                'object_code' => $payment->object->code ?? '',
                'total_amount' => $totalAmount,
                'periods' => []
            ];

            foreach($periods as $index => $period) {
                if ($index === 0) {
                    $amount = $payment->entries->where('date', '<=', $period['end'])->sum('amount');
                } else {
                    $amount = $payment->entries->whereBetween('date', [$period['start'], $period['end']])->sum('amount');
                }

                $paymentInfo['periods'][] = array_merge($period, ['amount' => $amount]);
            }

            $paymentsInfo['cf_payments'][] = $paymentInfo;
        }

        $paymentsInfo['other_payments'] = [];

        foreach($otherPlanPayments as $paymentName => $paymentAmount) {
            $paymentInfo = [
                'name' => $paymentName,
                'object_code' => '',
                'total_amount' => 0,
                'periods' => []
            ];

            $totalAmount = 0;
            foreach($periods as $index => $period) {
                if ($index === 0) {
                    $amount = $paymentAmount;
                } else {
                    $amount = 0;
                }

                $totalAmount += $amount;

                $paymentInfo['periods'][] = array_merge($period, ['amount' => $amount]);
            }

            $paymentInfo['total_amount'] = $totalAmount;

            $paymentsInfo['other_payments'][] = $paymentInfo;
        }

        $paymentsInfo['total_weeks'] = [
            'total_amount' => 0,
            'periods' => []
        ];

        $totalAmount = 0;
        foreach($periods as $index => $period) {
            if ($index === 0) {
                $amount = $CFPlanPaymentEntries->where('date', '<=', $period['end'])->sum('amount') + array_sum($otherPlanPayments);
            } else {
                $amount = $CFPlanPaymentEntries->whereBetween('date', [$period['start'], $period['end']])->sum('amount');
            }

            $totalAmount += $amount;

            $paymentsInfo['total_weeks']['periods'][] = array_merge($period, ['amount' => $amount]);
        }

        $paymentsInfo['total_weeks']['total_amount'] = $totalAmount;


        $paymentsInfo['balance_weeks'] = [
            'periods' => []
        ];

        foreach($periods as $index => $period) {
            $otherAmount = $plans->where('date', $period['start'])->where('reason_id', '!=', ReceivePlan::REASON_TARGET_AVANS)->sum('amount');

            if ($index === 0) {
                $amount = $CFPlanPaymentEntries->where('date', '<=', $period['end'])->sum('amount') + array_sum($otherPlanPayments);
            } else {
                $amount = $CFPlanPaymentEntries->whereBetween('date', [$period['start'], $period['end']])->sum('amount');
            }

            $diff = $otherAmount - $amount;

            $paymentsInfo['balance_weeks']['periods'][] = array_merge($period, ['amount' => $diff]);
        }


        $paymentsInfo['cum_balance_weeks'] = [
            'periods' => []
        ];

        $prev = 0;
        foreach($periods as $index => $period) {
            $otherAmount = $plans->where('date', $period['start'])->where('reason_id', '!=', ReceivePlan::REASON_TARGET_AVANS)->sum('amount');

            if ($index === 0) {
                $amount = $CFPlanPaymentEntries->where('date', '<=', $period['end'])->sum('amount') + array_sum($otherPlanPayments);
            } else {
                $amount = $CFPlanPaymentEntries->whereBetween('date', [$period['start'], $period['end']])->sum('amount');
            }

            $diff = $otherAmount - $amount + $prev;
            $prev = $diff;

            $paymentsInfo['cum_balance_weeks']['periods'][] = array_merge($period, ['amount' => $diff]);
        }

        $pivot = [
            'receive_total' => $receiveTotal,
            'receive' => $objectsInfo,
            'payment' => $paymentsInfo,
        ];

        return response()->json(['pivot' => $pivot]);
    }
}
