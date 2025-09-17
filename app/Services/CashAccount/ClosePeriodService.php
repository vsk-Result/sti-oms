<?php

namespace App\Services\CashAccount;

use App\Models\CashAccount\CashAccount;
use App\Models\CashAccount\CashAccountPayment;
use App\Models\CashAccount\ClosePeriod;
use Carbon\Carbon;
use DateTime;
use Illuminate\Database\Eloquent\Collection;

class ClosePeriodService
{
    public function getClosePeriods(CashAccount $cashAccount): Collection
    {
        return ClosePeriod::where('cash_account_id', $cashAccount->id)->orderByDesc('period')->get();
    }

    public function createClosePeriod(CashAccount $cashAccount, array $requestDate): void
    {
        $validPayments = CashAccountPayment::where('cash_account_id', $cashAccount->id)
            ->whereIn('status_id', [CashAccountPayment::STATUS_VALID, CashAccountPayment::STATUS_VALIDATED])
            ->where('date', 'LIKE', $requestDate['period'] . '-%')
            ->get();

        $activePayments = CashAccountPayment::where('cash_account_id', $cashAccount->id)
            ->where('status_id', CashAccountPayment::STATUS_ACTIVE)
            ->where('type_id', CashAccountPayment::TYPE_TRANSFER)
            ->where('date', 'LIKE', $requestDate['period'] . '-%')
            ->get();

        ClosePeriod::create([
            'cash_account_id' => $cashAccount->id,
            'period' => $requestDate['period'] . '-01',
            'payments_count' => $validPayments->count() + $activePayments->count(),
            'payments_amount' => $validPayments->sum('amount') + $activePayments->sum('amount'),
            'balance_when_closed' => $cashAccount->getBalance(),
        ]);

        foreach ($validPayments as $payment) {
            $payment->update([
                'status_id' => CashAccountPayment::STATUS_CLOSED
            ]);
        }

        foreach ($activePayments as $payment) {
            $payment->update([
                'status_id' => CashAccountPayment::STATUS_CLOSED
            ]);
        }
    }

    public function getPeriodsToClose(CashAccount $cashAccount): array
    {
        $activeStatuses = [CashAccountPayment::STATUS_ACTIVE, CashAccountPayment::STATUS_VALID, CashAccountPayment::STATUS_VALIDATED];
        $activePayments = $cashAccount->payments()->whereIn('status_id', $activeStatuses)->orderBy('date')->get();

        if ($activePayments->count() === 0) {
            return [];
        }

        $startPeriodDate = Carbon::parse($activePayments->first()->date)->format('Y-m');
        $endPeriodDate = Carbon::parse($activePayments->last()->date)->format('Y-m');

        $periodsToClose = [];
        $currentPeriod = Carbon::now()->format('Y-m');
        $periods = $this->getMonthsBetween($startPeriodDate, $endPeriodDate);

        foreach ($periods as $period) {

//            if ($currentPeriod >= $period) {
//                continue;
//            }
            $payments = $cashAccount->payments()->whereIn('status_id', $activeStatuses)->where('date', 'LIKE', $period . '-%')->get();
            if ($payments->count() === 0) {
                continue;
            }

            $transferApprovedPayments = $payments->where('status_id', CashAccountPayment::STATUS_ACTIVE)->where('type_id', '!=', CashAccountPayment::TYPE_OBJECT);
            $validPayments = $payments->whereIn('status_id', [CashAccountPayment::STATUS_VALID, CashAccountPayment::STATUS_VALIDATED]);

            if ($payments->count() !== ($validPayments->count() + $transferApprovedPayments->count())) {
                continue;
            }

            $existPeriod = ClosePeriod::where('cash_account_id', $cashAccount->id)->where('period', $period . '-01')->first();

            if ($existPeriod) {
                continue;
            }

            $periodsToClose[] = $period;
        }

        return $periodsToClose;
    }

    public function updateClosePeriod(ClosePeriod $closePeriod): void
    {
        $validPayments = CashAccountPayment::where('cash_account_id', $closePeriod->cash_account_id)
            ->whereIn('status_id', [CashAccountPayment::STATUS_VALID, CashAccountPayment::STATUS_VALIDATED])
            ->where('date', 'LIKE', substr($closePeriod->period, 0, 7) . '-%')
            ->get();

        $activePayments = CashAccountPayment::where('cash_account_id', $closePeriod->cash_account_id)
            ->where('status_id', CashAccountPayment::STATUS_ACTIVE)
            ->where('type_id', CashAccountPayment::TYPE_TRANSFER)
            ->where('date', 'LIKE', substr($closePeriod->period, 0, 7) . '-%')
            ->get();

        if ($validPayments->count() > 0) {
            foreach ($validPayments as $payment) {
                $payment->update([
                    'status_id' => CashAccountPayment::STATUS_CLOSED
                ]);
            }
        }

        if ($activePayments->count() > 0) {
            foreach ($activePayments as $payment) {
                $payment->update([
                    'status_id' => CashAccountPayment::STATUS_CLOSED
                ]);
            }
        }

        if ($validPayments->count() > 0 || $activePayments->count() > 0) {
            $closedPayments = CashAccountPayment::where('cash_account_id', $closePeriod->cash_account_id)
                ->where('status_id', CashAccountPayment::STATUS_CLOSED)
                ->where('date', 'LIKE', substr($closePeriod->period, 0, 7) . '-%')
                ->get();

            $closePeriod->update([
                'payments_count' => $closedPayments->count(),
                'payments_amount' => $closedPayments->sum('amount'),
            ]);
        }
    }

    function getMonthsBetween($startMonth, $endMonth)
    {
        // Создаем объекты DateTime из переданных строк
        $start = new DateTime($startMonth);
        $end   = new DateTime($endMonth);

        // Массив для хранения результатов
        $months = [];

        while ($start <= $end) {
            // Добавляем текущий месяц в массив
            $months[] = $start->format('Y-m');

            // Переходим к следующему месяцу
            $start->modify('+1 month');
        }

        return $months;
    }
}
