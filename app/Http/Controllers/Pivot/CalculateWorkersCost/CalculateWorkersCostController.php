<?php

namespace App\Http\Controllers\Pivot\CalculateWorkersCost;

use App\Http\Controllers\Controller;
use App\Models\Object\BObject;
use App\Services\Pivots\CalculateWorkersCost\CalculateWorkersCostService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CalculateWorkersCostController extends Controller
{
    public function __construct(private CalculateWorkersCostService $calculateWorkersCostService) {}

    public function index(Request $request): View
    {
        $filterYears = ['2026', '2025', '2024'];

        $years = $request->get('years', $filterYears);
        $objectIds = $request->get('object_id', []);

        $objects = BObject::active()->orderBy('code')->get();

        if (count($objectIds) > 0) {
            $infoByObjects = $this->calculateWorkersCostService->getPivotInfoByObjects($objectIds);
            return view('pivots.calculate-workers-cost.objects.index', compact('infoByObjects', 'filterYears', 'objects'));
        }

        $infoByCompany = $this->calculateWorkersCostService->getPivotInfoByCompany($years);
        return view('pivots.calculate-workers-cost.company.index', compact('infoByCompany', 'filterYears', 'objects'));
    }
}
