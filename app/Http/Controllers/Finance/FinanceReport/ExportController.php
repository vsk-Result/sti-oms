<?php

namespace App\Http\Controllers\Finance\FinanceReport;

use App\Exports\Finance\FinanceReport\Export;
use App\Http\Controllers\Controller;
use App\Models\Company;
use App\Models\FinanceReportHistory;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class ExportController extends Controller
{
    public function store(Request $request): BinaryFileResponse
    {
        $company = Company::where('name', 'ООО "Строй Техно Инженеринг"')->first();

        if ( !$company) {
            abort(404);
        }

        $date = Carbon::parse($request->get('date'));
        $financeReportHistory = FinanceReportHistory::where('date', $date->format('Y-m-d'))->first();

        if (!$financeReportHistory) {
            abort(404);
        }

        $balancesInfo = json_decode($financeReportHistory->balances);
        $creditsInfo = json_decode($financeReportHistory->credits);
        $loansInfo = json_decode($financeReportHistory->loans);
        $depositsInfo = json_decode($financeReportHistory->deposits);
        $objectsInfo = json_decode($financeReportHistory->objects);

        return Excel::download(
            new Export(
                [
                    'balancesInfo' => $balancesInfo,
                    'creditsInfo' => $creditsInfo,
                    'loansInfo' => $loansInfo,
                    'depositsInfo' => $depositsInfo,
                    'objectsInfo' => $objectsInfo
                ]
            ),
            'Финансовый отчет.xlsx'
        );
    }
}
