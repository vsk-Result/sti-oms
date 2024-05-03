<?php

namespace App\Http\Controllers\API\Loan;

use App\Http\Controllers\Controller;
use App\Models\CurrencyExchangeRate;
use App\Models\Loan;
use App\Models\Object\BObject;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class LoanController extends Controller
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

        $creditsArray = [];
        $loansArray = [];
        $info = [];
        $loans = Loan::orderByDesc('amount')->get();

        foreach ($loans as $loan) {
            $entry = [
                'total' => CurrencyExchangeRate::format($loan->total_amount, 'RUB'),
                'paid' => CurrencyExchangeRate::format($loan->getPaidAmount(), 'RUB'),
                'amount' => CurrencyExchangeRate::format($loan->amount, 'RUB'),
                'organization' => $loan->organization?->name,
            ];

            if ($loan->isCredit()) {
                $creditsArray[] = $entry;
            } else {
                $loansArray[] = $entry;
            }
        }

        $info['loans'] = $loansArray;
        $info['credits'] = $creditsArray;

        return response()->json(compact('info'));
    }
}
