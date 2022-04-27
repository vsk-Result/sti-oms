<?php

namespace App\Http\Controllers\Finance;

use App\Http\Controllers\Controller;
use App\Models\Company;
use App\Services\FinanceReport\AccountBalanceService;
use App\Services\FinanceReport\CreditService;
use App\Services\FinanceReport\DepositService;
use App\Services\FinanceReport\LoanService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\View\View;

class FinanceReportController extends Controller
{
    private AccountBalanceService $accountBalanceService;
    private CreditService $creditService;
    private LoanService $loanService;
    private DepositService $depositeService;

    public function __construct(
        AccountBalanceService $accountBalanceService,
        CreditService $creditService,
        LoanService $loanService,
        DepositService $depositeService
    ) {
        $this->accountBalanceService = $accountBalanceService;
        $this->creditService = $creditService;
        $this->loanService = $loanService;
        $this->depositeService = $depositeService;
    }

    public function index(Request $request): View
    {
        $company = Company::where('name', 'ООО "Строй Техно Инженеринг"')->first();

        if ( !$company) {
            abort(404);
        }

        $date = Carbon::parse($request->get('balance_date', now()));

        $balances = $this->accountBalanceService->getBalances($date, $company);

        $credits = $this->creditService->getCredits($date, $company);
        $totalCreditAmount = $this->creditService->getTotalCreditAmount($date, $company);

        $loans = $this->loanService->getLoans($date, $company);

        $deposites = $this->depositeService->getDeposites($date, $company);
        $depositesAmount = $this->depositeService->getDepositesTotalAmount($date, $company);

        return view(
            'finance-report.index',
            compact(
                'company', 'balances', 'date', 'credits',
                'totalCreditAmount', 'loans', 'deposites', 'depositesAmount'
            )
        );
    }
}
