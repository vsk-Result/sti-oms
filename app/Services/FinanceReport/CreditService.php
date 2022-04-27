<?php

namespace App\Services\FinanceReport;

use App\Models\Bank;
use App\Models\Company;
use App\Models\Payment;
use Carbon\Carbon;

class CreditService
{
    public function getCredits(string|Carbon $date, Company $company): array
    {
        $startCreditDate = Carbon::parse('2022-02-13');

        $credits = [
            [
                'bank_id' => 1,
                'bank' => Bank::getBankName(1),
                'contract' => '№ ВЛ/002020-006438 от 25.12.2020',
                'amount' => 100000000,
                'sent' => Payment::where('date', '>=', $startCreditDate)
                    ->where('date', '<=', $date)
                    ->where('description', 'LIKE', 'Погашение основного долга по договору № ВЛ/002020-006438 от 25.12.2020%')
                    ->sum('amount'),
                'received' => Payment::where('date', '>=', $startCreditDate)
                    ->where('date', '<=', $date)
                    ->where('description', 'LIKE', 'Выдача кредита по договору № ВЛ/002020-006438 от 25.12.2020%')
                    ->sum('amount')
            ]
        ];

        return $credits;
    }

    public function getTotalCreditAmount(string|Carbon $date, Company $company): float
    {
        $amount = 0;
        $credits = $this->getCredits($date, $company);

        foreach ($credits as $credit) {
            $amount += $credit['amount'] - $credit['amount'] - $credit['received'];
        }

        return $amount;
    }
}
