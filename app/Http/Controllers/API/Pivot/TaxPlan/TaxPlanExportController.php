<?php

namespace App\Http\Controllers\API\Pivot\TaxPlan;

use App\Exports\TaxPlan\Export;
use App\Http\Controllers\Controller;
use App\Services\TaxPlanItemService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class TaxPlanExportController extends Controller
{
    private TaxPlanItemService $taxPlanItemService;

    public function __construct(TaxPlanItemService $taxPlanItemService)
    {
        $this->taxPlanItemService = $taxPlanItemService;
    }

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

        $total = [];
        $items = $this->taxPlanItemService->filterTaxPlan(['filter' => 'current'], $total, false);

        $fileName = 'План_налогов_к_оплате_' . now()->format('d_m_Y') . '.pdf';
        Excel::store(new Export($items), '/exports/tax-plan/' . $fileName,
            'public',
            \Maatwebsite\Excel\Excel::MPDF
        );

        $url = config('app.url') . '/storage/exports/tax-plan/' . $fileName;

        return response()->json(['file' => [
            'name' => $fileName,
            'url' => $url
        ]]);
    }
}
