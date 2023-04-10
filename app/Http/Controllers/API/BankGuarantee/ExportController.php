<?php

namespace App\Http\Controllers\API\BankGuarantee;

use App\Exports\BankGuarantee\Export;
use App\Http\Controllers\Controller;
use App\Services\BankGuaranteeService;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class ExportController extends Controller
{
    private BankGuaranteeService $guaranteeService;

    public function __construct(BankGuaranteeService $guaranteeService)
    {
        $this->guaranteeService = $guaranteeService;
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

        $total = [];
        $bankGuarantees = $this->guaranteeService->filterBankGuarantee($request->toArray(), $total, false);
        $filename = 'bank_guarantees_' . Carbon::now()->format('d_m_Y') . '.xlsx';

        Excel::store(new Export($bankGuarantees), '/pivots/bankGuarantees/' . $filename);

        $url = config('app.url') . '/storage/pivots/bankGuarantees/' . $filename;

        return response()->json(['file' => [
            'name' => $filename,
            'url' => $url
        ]]);
    }
}
