<?php

namespace App\Http\Controllers\API\Finance;

use App\Exports\Finance\FinanceReport\Export;
use App\Http\Controllers\Controller;
use App\Models\FinanceReport;
use App\Models\FinanceReportHistory;
use App\Services\Contract\ActService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class FinanceReportExportController extends Controller
{
    private ActService $actService;

    public function __construct(ActService $actService)
    {
        $this->actService = $actService;
    }

    public function store(Request $request): JsonResponse
    {
//        if (! $request->has('verify_hash')) {
//            abort(403);
//            return response()->json([], 403);
//        }
//
//        if ($request->get('verify_hash') !== config('qr.verify_hash')) {
//            abort(403);
//            return response()->json([], 403);
//        }

        $financeReportHistory = FinanceReportHistory::where('date', now()->format('Y-m-d'))->first();

        if (!$financeReportHistory) {
            return response()->json(['error' => 'Финансовый отчет не найден'], 404);
        }

        $balancesInfo = json_decode($financeReportHistory->balances);
        $creditsInfo = json_decode($financeReportHistory->credits);
        $loansInfo = json_decode($financeReportHistory->loans);
        $depositsInfo = json_decode($financeReportHistory->deposits);
        $objectsInfo = json_decode($financeReportHistory->objects_new);
        $loansGroupInfo = FinanceReport::getLoansGroupInfo();

        $fileName = 'Финансовый_отчет_' . now()->format('d_m_Y') . '.xlsx';

        Excel::store(
            new Export(
                [
                    'balancesInfo' => $balancesInfo,
                    'creditsInfo' => $creditsInfo,
                    'loansInfo' => $loansInfo,
                    'depositsInfo' => $depositsInfo,
                    'objectsInfo' => $objectsInfo,
                    'loansGroupInfo' => $loansGroupInfo,
                ],
                [
                    'show_closed_objects' => false,
                    'act_service' => $this->actService,
                ]
            ),
            '/exports/finance/' . $fileName,
            'public',
            \Maatwebsite\Excel\Excel::XLSX
        );

        $url = config('app.url') . '/storage/exports/finance/' . $fileName;

        return response()->json(['file' => [
            'name' => $fileName,
            'url' => $url
        ]]);
    }
}
