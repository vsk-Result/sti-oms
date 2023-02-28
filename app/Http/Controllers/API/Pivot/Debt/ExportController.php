<?php

namespace App\Http\Controllers\API\Pivot\Debt;

use App\Exports\Pivot\Debt\Export;
use App\Http\Controllers\Controller;
use App\Services\DebtService;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class ExportController extends Controller
{
    private DebtService $debtService;

    public function __construct(DebtService $debtService)
    {
        $this->debtService = $debtService;
    }

    public function store(Request $request): JsonResponse
    {
        if (! $request->has('verify_hash')) {
            abort(403);
            return response()->json([], 403);
        }

        if ($request->get('verify_hash') !== config('qr.verify_hash')) {
            abort(403);
            return response()->json([], 403);
        }

        $pivot = $this->debtService->getPivot();
        $filename = 'Debts_from_STI_' . Carbon::now()->format('d_m_Y') . '.xlsx';

        Excel::store(new Export($pivot), '/pivots/debts/' . $filename);

        $url = config('app.url') . '/storage/pivots/debts/' . $filename;

        return response()->json(['file' => [
            'name' => $filename,
            'url' => $url
        ]]);
    }
}
