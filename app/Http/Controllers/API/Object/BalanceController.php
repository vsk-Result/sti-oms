<?php

namespace App\Http\Controllers\API\Object;

use App\Http\Controllers\Controller;
use App\Models\Contract\Contract;
use App\Models\CurrencyExchangeRate;
use App\Models\FinanceReportHistory;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class BalanceController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        if (! $request->has('verify_hash')) {
            return response()->json(['error' => 'Запрос не прошел валидацию'], 403);
        }

        if ($request->get('verify_hash') !== config('qr.verify_hash')) {
            return response()->json(['error' => 'Запрос не прошел валидацию'], 403);
        }

        if (! $request->has('access_objects')) {
            return response()->json(['error' => 'Отсутствует access_objects'], 403);
        }

        $info = [];
        $financeReportHistory = FinanceReportHistory::where('date', now()->format('Y-m-d'))->first();

        if (!$financeReportHistory) {
            return response()->json(compact('info'));
        }

        $accessObjects = null;
        if ($request->get('access_objects') !== '*') {
            $accessObjects = explode(',', $request->get('access_objects'));
        }

        $objectsInfo = json_decode($financeReportHistory->objects_new);
        $years = collect($objectsInfo->years)->toArray();
        $total = $objectsInfo->total;

        foreach($years as $year => $objects) {
            if ($year !== 'Активные') {
                continue;
            }
            foreach($objects as $object) {
                if (!is_null($accessObjects) && !in_array($object->id, $accessObjects)) {
                    continue;
                }

                $contractEndDate = '';
                $contractLastEndDate = Contract::where('object_id', $object->id)->where('end_date', '!=', null)->sortBy('end_date', SORT_NATURAL)->last();

                if ($contractLastEndDate) {
                    $contractEndDate = $contractLastEndDate->end_date;
                }

                $info[] = [
                    'id' => $object->id,
                    'title' => $object->code . ' | '  . $object->name,
                    'balance' => $total->{$year}->{$object->code}->{'objectBalance'},
                    'contract_end_date' => $contractEndDate,
                    'photo' => $object->getPhoto(),
                ];
            }
        }

        usort($info, function ($a, $b) {
            $codeA = substr($a['title'], 0, strpos($a['title'], ' |'));
            $codeB = substr($b['title'], 0, strpos($b['title'], ' |'));
            return $codeB - $codeA;
        });

        return response()->json(compact('info'));
    }
}
