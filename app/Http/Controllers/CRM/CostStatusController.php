<?php

namespace App\Http\Controllers\CRM;

use App\Http\Controllers\Controller;
use App\Models\CashAccount\CashAccount;
use App\Services\CashAccount\CashAccountService;
use App\Services\CashAccount\ClosePeriodService;
use App\Services\CashAccount\NotificationService;
use App\Services\CashAccount\Payment\PaymentService;
use Carbon\Carbon;
use Illuminate\View\View;
use DateTime;

class CostStatusController extends Controller
{
    public function __construct(
        private PaymentService $paymentService,
    ) {}

    public function index(): View
    {
        $closures = [];
        $notShowAccounts = ['Э.С.'];
        $nowMonth = Carbon::now()->format('Y-m') . '-01';
        $cashAccounts = CashAccount::where('status_id', CashAccount::STATUS_ACTIVE)->with('closePeriods')->get();

        foreach ($cashAccounts as $cashAccount) {
            if (in_array($cashAccount->name, $notShowAccounts)) {
                continue;
            }

            $closePeriodMonths = $cashAccount->closePeriods->sortByDesc('period')->pluck('period')->toArray();

            $closures[$cashAccount->id]['name'] = $cashAccount->name;
            $closures[$cashAccount->id]['not_close'] = [];
            $closures[$cashAccount->id]['not_split'] = [];

            $periods = $this->getMonthsBetween('2025-10', $nowMonth);

            foreach ($periods as $period) {
                $fp = $period . '-01';

                if ($fp === $nowMonth) {
                    continue;
                }
                if (! in_array($fp, $closePeriodMonths)) {
                    $closures[$cashAccount->id]['not_close'][] = Carbon::parse($fp)->format('F Y');
                }
            }
        }

        $realClosures = [];
        foreach ($closures as $cashAccountId => $closure) {
            $totalInfo = [];
            $payments = $this->paymentService->filterPayments(['cash_account_id' => [$cashAccountId]], true, $totalInfo);

            if ($payments > 0) {
                $realClosures[$cashAccountId] = $closure;
            }
        }

        $closures = $realClosures;

        return view('crm-costs.index', compact('closures'));
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
