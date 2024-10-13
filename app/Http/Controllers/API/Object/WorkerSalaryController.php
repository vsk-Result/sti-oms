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
        foreach ($objects as $object) {
            $objectInfo = [
                'id' => $object->id,
                'name' => $object->getName()
            ];
            $details = $object->getWorkSalaryDebtDetails();

            $summaryAmount = 0;
            foreach ($details as $detail) {
                $objectInfo['info'][] = [
                    'name' => 'Общее за ' . $detail['date'],
                    'sum' => $detail['amount']
                ];

                if (! isset($totalMonthsAmount[$detail['date']])) {
                    $totalMonthsAmount[$detail['date']] = 0;
                }

                $totalMonthsAmount[$detail['date']] += $detail['amount'];
                $summaryAmount += $detail['amount'];
            }

            if (!is_valid_amount_in_range($summaryAmount)) {
                continue;
            }

            $objectInfo['total_for_object'] = [
                'name' => 'Общее за объект ' . $object->getName(),
                'sum' => $summaryAmount
            ];

            $info['objects'][] = $objectInfo;
        }

        $totalAmount = 0;
        foreach ($totalMonthsAmount as $month => $amount) {
            $info['total']['total_list'][] = [
                'name' => 'Общее за ' . $month,
                'sum' => $amount
            ];
            $totalAmount += $amount;
        }

        $info['total']['total_for_all_object']['name'] = 'Общий долг';
        $info['total']['total_for_all_object']['sum'] = $totalAmount;

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
