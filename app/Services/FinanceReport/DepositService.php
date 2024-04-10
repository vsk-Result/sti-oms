<?php

namespace App\Services\FinanceReport;

use App\Models\BankGuarantee;
use App\Models\Company;
use App\Models\Deposit;
use App\Models\Status;
use App\Services\CurrencyExchangeRateService;
use Carbon\Carbon;

class DepositService
{
    private CurrencyExchangeRateService $rateService;

    public function __construct(CurrencyExchangeRateService $rateService)
    {
        $this->rateService = $rateService;
    }

    public function getDeposites(string|Carbon $date, Company $company): array
    {
        $bgDepositsRUB = BankGuarantee::where('status_id', Status::STATUS_ACTIVE)->where('end_date_deposit', '>=', Carbon::now())->where('currency', 'RUB')->sum('amount_deposit');
        $bgDepositsEUR = BankGuarantee::where('status_id', Status::STATUS_ACTIVE)->where('end_date_deposit', '>=', Carbon::now())->where('currency', 'EUR')->sum('amount_deposit');

        $depositsRUB = Deposit::where('status_id', Status::STATUS_ACTIVE)->where('end_date', '>=', Carbon::now())->where('currency', 'RUB')->sum('amount');
        $depositsEUR = Deposit::where('status_id', Status::STATUS_ACTIVE)->where('end_date', '>=', Carbon::now())->where('currency', 'EUR')->sum('amount');

        $rate = $this->rateService->getExchangeRate($date, 'EUR')->rate ?? 0;

        $deposites = [
            'Депозиты по БГ' => $bgDepositsRUB + ($bgDepositsEUR * $rate),
            'Депозиты' => $depositsRUB + ($depositsEUR * $rate),
        ];

        return $deposites;
    }

    public function getDepositesTotalAmount(string|Carbon $date, Company $company): float
    {
        return array_sum($this->getDeposites($date, $company));
    }
}
