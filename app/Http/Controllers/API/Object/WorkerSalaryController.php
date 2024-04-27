<?php

namespace App\Http\Controllers\API\Object;

use App\Http\Controllers\Controller;
use App\Models\CurrencyExchangeRate;
use App\Models\FinanceReportHistory;
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
            $objects = BObject::where('status_id', Status::STATUS_ACTIVE)->orderBy('code')->get();
        } else {
            $objects = BObject::where('object_id', $objectId)->get();
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
                    'sum' => CurrencyExchangeRate::format($detail['amount'], 'RUB')
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
                'sum' => CurrencyExchangeRate::format($summaryAmount, 'RUB')
            ];

            $info['objects'][] = $objectInfo;
        }

        $totalAmount = 0;
        foreach ($totalMonthsAmount as $month => $amount) {
            $info['total']['total_list'][] = [
                'name' => 'Общее за ' . $month,
                'sum' => CurrencyExchangeRate::format($amount, 'RUB')
            ];
            $totalAmount += $amount;
        }

        $info['total']['total_for_all_object']['name'] = 'Общий долг';
        $info['total']['total_for_all_object']['sum'] = CurrencyExchangeRate::format($totalAmount, 'RUB');

        return response()->json(compact('info'));
    }
}
