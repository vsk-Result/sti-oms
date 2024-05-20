<?php

namespace App\Http\Controllers\API\Pivot\Object;

use App\Http\Controllers\Controller;
use App\Models\CurrencyExchangeRate;
use App\Models\FinanceReportHistory;
use App\Models\Object\BObject;
use App\Models\Payment;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PivotController extends Controller
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

        if (empty($request->object_id)) {
            abort(404);
            return response()->json([], 404);
        }

        $object = BObject::find($request->object_id);

        if (! $object) {
            abort(404);
            return response()->json([], 404);
        }

        $financeReportHistory = FinanceReportHistory::where('date', now()->format('Y-m-d'))->first();

        $objectsInfo = json_decode($financeReportHistory->objects_new);
        $years = collect($objectsInfo->years)->toArray();
        $total = $objectsInfo->total;

        $info = [];

        foreach ($years as $year => $objects) {
            foreach ($objects as $o) {
                if ($o->id === $object->id) {
                    $info = (array) $total->{$year}->{$object->code};
                    break;
                }
            }
        }

        $percentFields = [
            'time_percent', 'complete_percent', 'money_percent', 'general_balance_to_receive_percentage',
            'fact_ready_percent', 'plan_ready_percent', 'deviation_plan_percent'
        ];
        $financeReportInfo = [];
        foreach ($info as $field => $amount) {
            if (in_array($field, $percentFields)) {
                $financeReportInfo[$field] = number_format($amount, 2) . '%';
                continue;
            }

            $financeReportInfo[$field] = $amount;
        }

        $financeReportInfo['pay_opste'] = Payment::where('object_id', $object->id)->where('category', Payment::CATEGORY_OPSTE)->where('amount', '<', 0)->sum('amount');
        $financeReportInfo['pay_rad'] = Payment::where('object_id', $object->id)->where('category', Payment::CATEGORY_RAD)->where('amount', '<', 0)->sum('amount');
        $financeReportInfo['pay_material'] = Payment::where('object_id', $object->id)->where('category', Payment::CATEGORY_MATERIAL)->where('amount', '<', 0)->sum('amount');
        $financeReportInfo['pay_salary'] = Payment::where('object_id', $object->id)->where('category', Payment::CATEGORY_SALARY)->where('amount', '<', 0)->sum('amount');
        $financeReportInfo['pay_tax'] = Payment::where('object_id', $object->id)->where('category', Payment::CATEGORY_TAX)->where('amount', '<', 0)->sum('amount');
        $financeReportInfo['pay_customers'] = Payment::where('object_id', $object->id)->where('category', Payment::CATEGORY_CUSTOMERS)->where('amount', '<', 0)->sum('amount');
        $financeReportInfo['pay_transfer'] = Payment::where('object_id', $object->id)->where('category', Payment::CATEGORY_TRANSFER)->where('amount', '<', 0)->sum('amount');
        $financeReportInfo['pay_empty'] = Payment::where('object_id', $object->id)->where(function ($q) {
            $q->whereNull('category');
            $q->orWhere('category', '');
        })->where('amount', '<', 0)->sum('amount');

        $info = array_merge(['name' => $object->getName()], $financeReportInfo);

        return response()->json(compact('info'));
    }
}