<?php

namespace App\Http\Controllers\CRM;

use App\Http\Controllers\Controller;
use App\Models\CashAccount\CashAccount;
use Carbon\Carbon;
use Illuminate\View\View;
use DateTime;

class CostStatusController extends Controller
{
    public function index(): View
    {
        $closures = [];
        $notShowAccounts = ['Э.С.'];
        $nowMonth = Carbon::now()->format('Y-m');
        $cashAccounts = CashAccount::where('status_id', CashAccount::STATUS_ACTIVE)->with('closePeriods')->get();

        foreach ($cashAccounts as $cashAccount) {
            if (in_array($cashAccount->name, $notShowAccounts)) {
                continue;
            }

            $closePeriods = $cashAccount->closePeriods->sortByDesc('period');

            $closures[$cashAccount->id]['name'] = $cashAccount->name;
            $closures[$cashAccount->id]['not_close'] = [];
            $closures[$cashAccount->id]['not_split'] = [];

            $periods = $this->getMonthsBetween('2025-10', $nowMonth);
            $closePeriodMonths = $closePeriods->pluck('period')->toArray();

            foreach ($periods as $period) {
                if ($period === $nowMonth) {
                    continue;
                }

                if (! in_array($period, $closePeriodMonths)) {
                    $closures[$cashAccount->id]['not_close'][] = Carbon::parse($period . '-01')->format('F Y');
                }
            }
        }

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
