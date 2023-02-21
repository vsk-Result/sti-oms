<?php

namespace App\Http\Controllers\Finance\FinanceReport;

use App\Exports\Finance\FinanceReport\Export;
use App\Http\Controllers\Controller;
use App\Models\Company;
use App\Models\Loan;
use App\Services\Contract\ContractService;
use App\Services\FinanceReport\AccountBalanceService;
use App\Services\FinanceReport\CreditService;
use App\Services\FinanceReport\DepositService;
use App\Services\FinanceReport\LoanService;
use Carbon\Carbon;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class ExportController extends Controller
{
    private AccountBalanceService $accountBalanceService;
    private CreditService $creditService;
    private LoanService $loanService;
    private DepositService $depositeService;
    private ContractService $contractService;

    public function __construct(
        AccountBalanceService $accountBalanceService,
        CreditService $creditService,
        LoanService $loanService,
        DepositService $depositeService,
        ContractService $contractService
    ) {
        $this->accountBalanceService = $accountBalanceService;
        $this->creditService = $creditService;
        $this->loanService = $loanService;
        $this->depositeService = $depositeService;
        $this->contractService = $contractService;
    }

    public function store(): BinaryFileResponse
    {
        $company = Company::where('name', 'ООО "Строй Техно Инженеринг"')->first();

        if ( !$company) {
            abort(404);
        }

        $date = now();

        $balances = $this->accountBalanceService->getBalances($date, $company);

        $credits = $this->creditService->getCredits($date, $company);
        $totalCreditAmount = $this->creditService->getTotalCreditAmount($date, $company);

        $loans = $this->loanService->getLoans($date, $company);

        $deposites = $this->depositeService->getDeposites($date, $company);
        $depositesAmount = $this->depositeService->getDepositesTotalAmount($date, $company);

        return Excel::download(
            new Export(
                [
                    'balances' => $balances,
                    'credits' => [
                        'credits' => $credits,
                        'totalAmount' => $totalCreditAmount
                    ],
                    'loans' => [
                        'loans' => $loans,
                        'totalAmount' => $loans->sum('amount')
                     ],
                    'deposites' => [
                        'deposites' => $deposites,
                        'totalAmount' => $depositesAmount
                    ],
                ],
                $this->contractService
            ),
            'Финансовый отчет.xlsx'
        );
    }
}
