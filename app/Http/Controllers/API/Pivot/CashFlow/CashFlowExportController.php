<?php

namespace App\Http\Controllers\API\Pivot\CashFlow;

use App\Exports\Pivot\CashFlow\Export;
use App\Http\Controllers\Controller;
use App\Services\ReceivePlanService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class CashFlowExportController extends Controller
{
    public function __construct(private ReceivePlanService $receivePlanService) {}

    public function index(Request $request): JsonResponse
    {
        if (! $request->has('verify_hash')) {
            abort(403);
            return response()->json([], 403);
        }

        if ($request->get('verify_hash') !== config('qr.verify_hash')) {
            abort(403);
            return response()->json([], 403);
        }

        $fileName = 'Отчет_CASH_FLOW_' . now()->format('d_m_Y') . '.xlsx';
        Excel::store(new Export($this->receivePlanService, []), '/exports/cash-flow/' . $fileName,
            'public',
            \Maatwebsite\Excel\Excel::XLSX
        );

        $url = config('app.url') . '/storage/exports/cash-flow/' . $fileName;

        return response()->json(['file' => [
            'name' => $fileName,
            'url' => $url
        ]]);
    }
}
