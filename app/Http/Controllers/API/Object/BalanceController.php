<?php

namespace App\Http\Controllers\API\Object;

use App\Http\Controllers\Controller;
use App\Models\CurrencyExchangeRate;
use App\Models\FinanceReportHistory;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class BalanceController extends Controller
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

        $info = [];
        $financeReportHistory = FinanceReportHistory::where('date', now()->format('Y-m-d'))->first();

        if (!$financeReportHistory) {
            return response()->json(compact('info'));
        }

        $objectsInfo = json_decode($financeReportHistory->objects_new);
        $years = collect($objectsInfo->years)->toArray();
        $total = $objectsInfo->total;

        foreach($years as $year => $objects) {
            foreach($objects as $object) {
                $info[] = [
                    'id' => $object->id,
                    'title' => $object->code . ' | '  . $object->name,
                    'balance' => CurrencyExchangeRate::format($total->{$year}->{$object->code}->{'objectBalance'}, 'RUB'),
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
