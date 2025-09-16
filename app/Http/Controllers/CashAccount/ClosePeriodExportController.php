<?php

namespace App\Http\Controllers\CashAccount;

use App\Exports\CashAccount\Payment\Export;
use App\Http\Controllers\Controller;
use App\Models\CashAccount\CashAccount;
use App\Models\CashAccount\CashAccountPayment;
use App\Models\CashAccount\ClosePeriod;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class ClosePeriodExportController extends Controller
{
    public function store(CashAccount $cashAccount, ClosePeriod $closePeriod): BinaryFileResponse
    {
        $period = substr($closePeriod->period, 0, 7);
        $payments = CashAccountPayment::where('cash_account_id', $cashAccount->id)
            ->where('date', 'LIKE', $period . '-%')
            ->where('status_id', CashAccountPayment::STATUS_CLOSED)
            ->orderBy('date');

        return Excel::download(new Export($payments), 'Оплаты закрытого периода ' . translate_year_month($period) . ' по кассе ' . $cashAccount->name . '.xlsx');
    }
}
