<?php

namespace App\Services\FinanceReport;

use App\Models\Bank;
use App\Models\Company;
use App\Models\PaymentImport;
use Carbon\Carbon;

class AccountBalanceService
{
    public function getBalances(string|Carbon $date, Company $company): array
    {
        $banks = Bank::getBanks();
        $exceptBanks = [5]; // Росбанк для СТИ не нужно показывать
        $currencies = ['RUB', 'EUR'];

        $balances = [
            'total' => [],
            'banks' => [],
        ];

        foreach ($currencies as $currency) {
            $balances['total'][$currency] = 0;
        }

        foreach ($banks as $bankId => $bankName) {

            if (in_array($bankId, $exceptBanks)) {
                continue;
            }

            foreach ($currencies as $currency) {
                $balance = PaymentImport::where('company_id', $company->id)
                    ->where('date', '<=', $date)
                    ->where('bank_id', $bankId)
                    ->where('currency', $currency)
                    ->orderBy('date', 'desc')
                    ->first();

                $balances['banks'][$bankName][$currency] = $balance->outgoing_balance ?? 0;
                $balances['total'][$currency] += $balances['banks'][$bankName][$currency];
            }
        }

        return $balances;
    }
}
