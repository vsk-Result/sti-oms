<?php

namespace App\Http\Controllers\API\Object;

use App\Http\Controllers\Controller;
use App\Models\CurrencyExchangeRate;
use App\Models\FinanceReportHistory;
use App\Models\Loan;
use App\Models\Object\BObject;
use App\Models\Status;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class WorkerSalaryController extends Controller
{
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

        $objectId = null;
        if ($request->has('object_id')) {
            $objectId = $request->get('object_id');
            $obj = BObject::find($objectId);

            if (! $obj) {
                abort(404);
                return response()->json([], 404);
            }
        }

        $info = [];

        if (is_null($objectId)) {
            if (! $request->has('access_objects')) {
                return response()->json(['error' => 'Отсутствует access_objects'], 403);
            }

            $objects = BObject::where('status_id', Status::STATUS_ACTIVE)->orderBy('code')->get();

            if ($request->get('access_objects') !== '*') {
                $accessObjects = explode(',', $request->get('access_objects'));
                $objects = BObject::where('status_id', Status::STATUS_ACTIVE)->whereIn('id', $accessObjects)->orderBy('code')->get();
            }

        } else {
            $objects = BObject::where('id', $objectId)->get();
        }

        $totalMonthsAmount = [];
        $totalWorkersSalaryMonthsAmount = [];
        $totalITRSalaryMonthsAmount = [];
        foreach ($objects as $object) {
            $objectInfo = [
                'id' => $object->id,
                'name' => $object->getName(),
                'workers_salary' => [],
                'itr_salary' => [],
                'total_for_object_workers_salary' => [],
                'total_for_object_itr_salary' => [],
                'total_for_object' => [],
            ];

            $workersSalaryDetails = $object->getWorkSalaryDebtDetails();

            $summaryWorkersSalaryAmount = 0;
            foreach ($workersSalaryDetails as $detail) {
                $objectInfo['workers_salary'][] = [
                    'name' => 'Общее за ' . $detail['date'],
                    'sum' => $detail['amount']
                ];

                if (! isset($totalMonthsAmount[$detail['date']])) {
                    $totalMonthsAmount[$detail['date']] = 0;
                }

                if (! isset($totalWorkersSalaryMonthsAmount[$detail['date']])) {
                    $totalWorkersSalaryMonthsAmount[$detail['date']] = 0;
                }

                $totalMonthsAmount[$detail['date']] += $detail['amount'];
                $totalWorkersSalaryMonthsAmount[$detail['date']] += $detail['amount'];
                $summaryWorkersSalaryAmount += $detail['amount'];
            }

            $itrSalaryDetails = $object->getITRSalaryDebtDetails();

            $summaryITRSalaryAmount = 0;
            foreach ($itrSalaryDetails as $detail) {
                $objectInfo['itr_salary'][] = [
                    'name' => 'Общее за ' . $detail['formatted_date'],
                    'sum' => $detail['amount']
                ];

                if (! isset($totalMonthsAmount[$detail['formatted_date']])) {
                    $totalMonthsAmount[$detail['formatted_date']] = 0;
                }

                if (! isset($totalITRSalaryMonthsAmount[$detail['formatted_date']])) {
                    $totalITRSalaryMonthsAmount[$detail['formatted_date']] = 0;
                }

                $totalMonthsAmount[$detail['formatted_date']] += $detail['amount'];
                $totalITRSalaryMonthsAmount[$detail['formatted_date']] += $detail['amount'];
                $summaryITRSalaryAmount += $detail['amount'];
            }


            if (is_valid_amount_in_range($summaryWorkersSalaryAmount + $summaryITRSalaryAmount)) {
                $objectInfo['total_for_object_workers_salary'] = [
                    'name' => 'Общее за объект ' . $object->getName(),
                    'sum' => $summaryWorkersSalaryAmount
                ];

                $objectInfo['total_for_object_itr_salary'] = [
                    'name' => 'Общее за объект ' . $object->getName(),
                    'sum' => $summaryITRSalaryAmount
                ];

                $objectInfo['total_for_object'] = [
                    'name' => 'Общее за объект ' . $object->getName(),
                    'sum' => $summaryWorkersSalaryAmount + $summaryITRSalaryAmount
                ];

                $info['salary'][] = $objectInfo;
            }
        }

        $totalWorkersSalaryAmount = 0;
        $totalITRSalaryAmount = 0;
        $totalAmount = 0;
        foreach ($totalWorkersSalaryMonthsAmount as $month => $amount) {
            $info['total']['workers_salary']['total_list'][] = [
                'name' => 'Общее за ' . $month,
                'sum' => $amount
            ];
            $totalAmount += $amount;
            $totalWorkersSalaryAmount += $amount;
        }

        foreach ($totalITRSalaryMonthsAmount as $month => $amount) {
            $info['total']['itr_salary']['total_list'][] = [
                'name' => 'Общее за ' . $month,
                'sum' => $amount
            ];
            $totalITRSalaryAmount += $amount;
            $totalAmount += $amount;
        }

        foreach ($totalMonthsAmount as $month => $amount) {
            $info['total']['salary']['total_list'][] = [
                'name' => 'Общее за ' . $month,
                'sum' => $amount
            ];
        }

        $info['total']['workers_salary']['total_for_all_object']['name'] = 'Общий долг';
        $info['total']['workers_salary']['total_for_all_object']['sum'] = $totalWorkersSalaryAmount;

        $info['total']['itr_salary']['total_for_all_object']['name'] = 'Общий долг';
        $info['total']['itr_salary']['total_for_all_object']['sum'] = $totalITRSalaryAmount;

        $info['total']['salary']['total_for_all_object']['name'] = 'Общий долг';
        $info['total']['salary']['total_for_all_object']['sum'] = $totalAmount;

        $info['credits'] = [];
        $info['loans'] = [];

        $info['total']['credits_total'] = 0;
        $info['total']['loans_total'] = 0;

        $financeReportHistory = FinanceReportHistory::where('date', now()->format('Y-m-d'))->first();

        if (!$financeReportHistory) {
            return response()->json(compact('info'));
        }

        $creditsInfo = json_decode($financeReportHistory->credits);
        $loansInfo = json_decode($financeReportHistory->loans);

        foreach ($creditsInfo->credits as $credit) {
            if (isset($credit->credit_type_id) && $credit->credit_type_id === Loan::CREDIT_TYPE_DEFAULT) {
                $info['credits'][] = [
                    'title' => $credit->bank,
                    'description' => $credit->contract,
                    'amount' => (float) $credit->debt,
                ];
            } else {
                $info['credits'][] = [
                    'title' => $credit->bank,
                    'description' => $credit->contract,
                    'amount' => (float) $credit->paid ?? 0,
                ];
            }
        }

        foreach ($loansInfo->loans as $loan) {
            $info['loans'][] = [
                'title' => $loan->organization->name,
                'description' => $loan->name,
                'amount' => (float) $loan->amount,
            ];
        }

        $info['total']['credits_total'] = (float) $creditsInfo->totalCreditAmount;
        $info['total']['loans_total'] = (float) $loansInfo->totalLoanAmount;

        return response()->json(compact('info'));
    }
}
