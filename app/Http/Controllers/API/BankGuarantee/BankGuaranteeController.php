<?php

namespace App\Http\Controllers\API\BankGuarantee;

use App\Http\Controllers\Controller;
use App\Models\BankGuarantee;
use App\Models\CurrencyExchangeRate;
use App\Models\Object\BObject;
use App\Services\BankGuaranteeService;
use App\Services\Contract\ActService;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class BankGuaranteeController extends Controller
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

        if (! $request->has('start_date') || ! $request->has('end_date') || ! $request->has('value')) {
            abort(404);
            return response()->json([], 404);
        }

        if (! in_array($request->get('value'), ['guarantee', 'deposite'])) {
            abort(404);
            return response()->json([], 404);
        }

        $isGuarantee = $request->get('value') === 'guarantee';
        $startDate = Carbon::parse($request->get('start_date'))->format('Y-m') . '-01';
        $endDate = Carbon::parse($request->get('end_date'))->format('Y-m') . '-' . Carbon::parse($request->get('end_date'))->lastOfMonth()->day;

        $bankGuaranteesQuery = BankGuarantee::query();

        if ($isGuarantee) {
            $bankGuaranteesQuery->whereBetween('end_date', [$startDate, $endDate]);
            $bankGuaranteesQuery->where('end_date', '>=', Carbon::now())->orderBy('end_date');
        } else {
            $bankGuaranteesQuery->whereBetween('end_date_deposit', [$startDate, $endDate]);
            $bankGuaranteesQuery->where('end_date_deposit', '>=', Carbon::now())->orderBy('end_date_deposit');
        }

        $bankGuarantees = $bankGuaranteesQuery->get();

        $answer = [];
        foreach ($bankGuarantees as $bankGuarantee) {
            if ($isGuarantee) {
                if (! empty($bankGuarantee->end_date) && $bankGuarantee->amount > 0) {
                    $key = Carbon::parse($bankGuarantee->end_date)->format('m-Y');
                    $subkey = Carbon::parse($bankGuarantee->end_date)->format('d-m-Y');
                    $currency = $bankGuarantee->currency;

                    if (! isset($answer[$key][$subkey][$currency])) {
                        $answer[$key][$subkey][$currency] = 0;
                    }

                    $answer[$key][$subkey][$currency] += $bankGuarantee->amount;
                }
            } else {
                if (! empty($bankGuarantee->end_date_deposit) && $bankGuarantee->amount_deposit > 0) {
                    $key = Carbon::parse($bankGuarantee->end_date_deposit)->format('m-Y');
                    $subkey = Carbon::parse($bankGuarantee->end_date_deposit)->format('d-m-Y');
                    $currency = $bankGuarantee->currency;

                    if (! isset($answer[$key][$subkey][$currency])) {
                        $answer[$key][$subkey][$currency] = 0;
                    }

                    $answer[$key][$subkey][$currency] += $bankGuarantee->amount_deposit;
                }
            }
        }

        return response()->json(compact('answer'));
    }
}
