<?php

namespace App\Http\Controllers\API\Pivot\Payment;

use App\Http\Controllers\Controller;
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

        $startDate = $request->get('start_date');
        $endDate = $request->get('end_date');
        $object = $request->get('object');

        $paymentQuery = Payment::select('date', 'object_id', 'amount');
        $generalCostsQuery = GeneralCost::query();

        if ($date !== 'all_period') {
            $paymentQuery->whereBetween('date', [Carbon::parse($startDate)->format('Y-m-d'), Carbon::parse($endDate)->format('Y-m-d')]);
        }

        if ($object !== 'all_objects') {
            $paymentQuery->where('object_id', $object);
            $generalCostsQuery->where('object_id', $object);
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
