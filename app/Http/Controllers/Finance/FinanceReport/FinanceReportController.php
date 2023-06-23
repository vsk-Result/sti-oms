<?php

namespace App\Http\Controllers\Finance\FinanceReport;

use App\Http\Controllers\Controller;
use App\Models\Company;
use App\Models\FinanceReportHistory;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use function abort;
use function now;
use function view;

class FinanceReportController extends Controller
{
    public function index(Request $request): View
    {
        $company = Company::where('name', 'ООО "Строй Техно Инженеринг"')->first();

        if ( !$company) {
            abort(404);
        }

        $date = Carbon::parse($request->get('balance_date', now()));
        $financeReportHistory = FinanceReportHistory::where('date', $date->format('Y-m-d'))->first();

        if (!$financeReportHistory) {
            abort(404);
        }

        $balancesInfo = json_decode($financeReportHistory->balances);
        $creditsInfo = json_decode($financeReportHistory->credits);
        $loansInfo = json_decode($financeReportHistory->loans);
        $depositsInfo = json_decode($financeReportHistory->deposits);
        $objectsInfo = json_decode($financeReportHistory->objects);

        return view(
            'finance-report.index',
            compact(
                'company', 'balancesInfo',
                'date', 'creditsInfo', 'loansInfo', 'depositsInfo', 'objectsInfo'
            )
        );
    }
}
