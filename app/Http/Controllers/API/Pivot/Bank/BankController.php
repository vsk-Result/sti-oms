<?php

namespace App\Http\Controllers\API\Pivot\Bank;

use App\Http\Controllers\Controller;
use App\Models\Company;
use App\Models\Loan;
use App\Models\Object\GeneralCost;
use App\Models\Payment;
use App\Models\PaymentImport;
use App\Services\FinanceReport\AccountBalanceService;
use App\Services\FinanceReport\CreditService;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class BankController extends Controller
{
    private AccountBalanceService $accountBalanceService;
    private CreditService $creditService;

    public function __construct(
        AccountBalanceService $accountBalanceService,
        CreditService $creditService,
    ) {
        $this->accountBalanceService = $accountBalanceService;
        $this->creditService = $creditService;
    }

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

        $company = Company::getSTI();

        if ( !$company) {
            abort(404);
            return response()->json([], 404);
        }

        $date = now();
        $balances = $this->accountBalanceService->getBalances($date, $company);
        $balancesLastUpdateDate = PaymentImport::orderByDesc('date')->first()->created_at;

        $credits = $this->creditService->getCredits($date, $company);
        $totalCreditAmount = $this->creditService->getTotalCreditAmount($date, $company);
        $creditsLastUpdateDate = Loan::where('type_id', Loan::TYPE_CREDIT)->latest('updated_at')->first()->updated_at;

        $response = [
            'balances' => $balances,
            'balances_last_update_date' => $balancesLastUpdateDate,
            'credits' => $credits,
            'total_credit_debt' => $totalCreditAmount,
            'credits_last_update_date' => $creditsLastUpdateDate,
        ];

        return response()->json(compact('response'));
    }
}
