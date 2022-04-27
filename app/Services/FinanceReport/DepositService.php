<?php

namespace App\Services\FinanceReport;

use App\Models\BankGuarantee;
use App\Models\Company;
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
        $deposites = [
            'RUB' => BankGuarantee::where('currency', 'RUB')->sum('amount_deposit'),
            'EUR' => BankGuarantee::where('currency', 'EUR')->sum('amount_deposit'),
        ];

        return $deposites;
    }

    public function getDepositesTotalAmount(string|Carbon $date, Company $company): float
    {
        $deposites = $this->getDeposites($date, $company);
        $rate = $this->rateService->getExchangeRate($date, 'EUR')->rate;

        return $deposites['RUB'] + $rate * $deposites['EUR'];
    }
}
