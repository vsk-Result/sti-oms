<?php

namespace App\Services\FinanceReport;

use App\Models\Bank;
use App\Models\Company;
use App\Models\Loan;
use App\Models\Payment;
use Carbon\Carbon;

class CreditService
{
    public function getCredits(string|Carbon $date, Company $company): array
    {
        $credits = [];
        $creditsList = Loan::where('type_id', Loan::TYPE_CREDIT)->with('organization')->get();

        foreach ($creditsList as $credit) {
            if ($credit->total_amount == 0) {
                continue;
            }

            $credits[] = [
                'id' => $credit->id,
                'credit_type_id' => $credit->credit_type_id,
                'bank' => $credit->getBankName(),
                'contract' => $credit->name,
                'total' => $credit->total_amount,
                'paid' => abs($credit->paid_amount),
                'debt' => $credit->amount,
            ];
        }

        return $credits;

        // старая ручная реализация
        $startCreditDate = '2022-02-15';

        $credits = [
            [
                'bank_id' => 1,
                'bank' => Bank::getBankName(1),
                'contract' => '№ ВЛ/002020-006438 от 25.12.2020',
                'amount' => 100000000,
                'sent' => Payment::whereBetween('date', [$startCreditDate, $date])
                    ->where('description', 'LIKE', 'Погашение основного долга по договору № ВЛ/002020-006438 от 25.12.2020%')
                    ->sum('amount'),
                'received' => Payment::whereBetween('date', [$startCreditDate, $date])
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
            $amount += $credit['debt'];
        }

        return $amount;

        // старая ручная реализация
        $amount = 0;
        $credits = $this->getCredits($date, $company);

        foreach ($credits as $credit) {
            $amount += $credit['sent'] + $credit['received'];
        }

        return -$amount;
    }
}
