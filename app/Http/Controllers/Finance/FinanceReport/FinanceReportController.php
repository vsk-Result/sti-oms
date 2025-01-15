<?php

namespace App\Http\Controllers\Finance\FinanceReport;

use App\Http\Controllers\Controller;
use App\Models\Company;
use App\Models\FinanceReport;
use App\Models\FinanceReportHistory;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\View\View;

class FinanceReportController extends Controller
{
    public function index(Request $request): View
    {
        $company = Company::getSTI();

        if ( !$company) {
            abort(404);
        }

        if ($request->has('balance_date')) {
            $date = Carbon::parse($request->get('balance_date'));
        } else {
            $date = now();

            if ($fr = FinanceReportHistory::select('date')->latest('date')->first()) {
                $date = Carbon::parse($fr->date);
            }
        }

        $financeReportHistory = FinanceReportHistory::where('date', $date->format('Y-m-d'))->first();

        if (!$financeReportHistory) {
            abort(404);
        }

        $balancesInfo = json_decode($financeReportHistory->balances);
        $creditsInfo = json_decode($financeReportHistory->credits);
        $loansInfo = json_decode($financeReportHistory->loans);
        $depositsInfo = json_decode($financeReportHistory->deposits);
        $objectsInfo = json_decode($financeReportHistory->objects_new);
        $loansGroupInfo = FinanceReport::getLoansGroupInfo();


        return view(
            'finance-report.index',
            compact(
                'company', 'balancesInfo',
                'date', 'creditsInfo', 'loansInfo', 'depositsInfo', 'objectsInfo', 'loansGroupInfo'
            )
        );
    }
}
