<?php

namespace App\Http\Controllers\API\Pivot\Payment;

use App\Http\Controllers\Controller;
use App\Models\CurrencyExchangeRate;
use App\Models\FinanceReportHistory;
use App\Models\Object\BObject;
use App\Models\Object\GeneralCost;
use App\Models\Payment;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PaymentController extends Controller
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

        if (! $request->has('date') || ! $request->has('object')) {
            abort(404);
            return response()->json([], 404);
        }

        $date = $request->get('date');

        if ($date === 'period') {
            if (! $request->has('start_date') || ! $request->has('end_date')) {
                abort(404);
                return response()->json([], 404);
            }
        }

        $object = $request->get('object');

        $paymentQuery = Payment::select('date', 'object_id', 'amount');
        $generalCostsQuery = GeneralCost::query();

        if ($date === 'all_period') {
            $response = [];
            $financeReportHistory = FinanceReportHistory::where('date', now()->format('Y-m-d'))->first();

            if (!$financeReportHistory) {
                return response()->json(compact('response'));
            }

            $objectsInfo = json_decode($financeReportHistory->objects_new);

            if ($object === 'all_objects') {
                $summary = $objectsInfo->summary;
                $response = [
                    'total_without_general' => $summary->{'Активные'}->{'balance'},
                    'total_with_general' => $summary->{'Активные'}->{'balance_with_general_balance'},
                    'general' => $summary->{'Активные'}->{'general_balance'},
                    'pay' => $summary->{'Активные'}->{'pay'},
                    'receive' => $summary->{'Активные'}->{'receive'},
                ];
            } else {
                $years = collect($objectsInfo->years)->toArray();
                $total = $objectsInfo->total;
                foreach($years as $year => $objects) {
                    if ($year !== 'Активные') {
                        continue;
                    }
                    foreach($objects as $o) {
                        if ($o->id == $object) {
                            $response = [
                                'total_without_general' => $total->{$year}->{$o->code}->{'balance'},
                                'total_with_general' => $total->{$year}->{$o->code}->{'balance_with_general_balance'},
                                'general' => $total->{$year}->{$o->code}->{'general_balance'},
                                'pay' => $total->{$year}->{$o->code}->{'pay'},
                                'receive' => $total->{$year}->{$o->code}->{'receive'},
                            ];
                            break;
                        }
                    }
                }
            }

            return response()->json(compact('response'));
        }

        $startDate = Carbon::parse($request->get('start_date'))->format('Y-m') . '-01';
        $endDate = Carbon::parse($request->get('end_date'))->format('Y-m') . '-' . Carbon::parse($request->get('end_date'))->lastOfMonth()->day;
        $paymentQuery->whereBetween('date', [$startDate, $endDate]);

        if ($object !== 'all_objects') {
            $paymentQuery->where('object_id', $object);
            $generalCostsQuery->where('object_id', $object);
        } else {
            $objectIds = BObject::active()->pluck('id')->toArray();
            $paymentQuery->whereIn('object_id', $objectIds);
            $generalCostsQuery->whereIn('object_id', $objectIds);
        }

        $pay = (float) (clone $paymentQuery)->where('amount', '<', 0)->sum('amount');
        $receive = (float) (clone $paymentQuery)->sum('amount') - $pay;
        $totalWithoutGeneral = $pay + $receive;
        $totalWithGeneral = $totalWithoutGeneral + $generalCostsQuery->sum('amount');
        $general = $totalWithGeneral - $totalWithoutGeneral;

        $response = [
            'total_without_general' => $totalWithoutGeneral,
            'total_with_general' => $totalWithGeneral,
            'general' => $general,
            'pay' => $pay,
            'receive' => $receive,
        ];

        return response()->json(compact('response'));
    }
}
