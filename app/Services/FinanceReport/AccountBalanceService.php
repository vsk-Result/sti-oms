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

        $balances = [];
        foreach ($banks as $bankId => $bankName) {

            if (in_array($bankId, $exceptBanks)) {
                continue;
            }

            $balance = PaymentImport::where('company_id', $company->id)
                ->where('date', '<=', $date)
                ->where('bank_id', $bankId)
                ->orderBy('date', 'desc')
                ->first();

            $balances[$bankName] = $balance->outgoing_balance ?? 0;
        }

        return $balances;
    }
}
