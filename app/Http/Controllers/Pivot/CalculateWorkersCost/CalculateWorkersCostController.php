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
        $year = $request->get('year', date('Y'));
        $objectIds = $request->get('object_id', []);

        $years = ['2025', '2024', '2023', '2022'];
        $objects = BObject::active()->orderBy('code')->get();

        if (count($objectIds) > 0) {
            $infoByObjects = $this->calculateWorkersCostService->getPivotInfoByObjects($objectIds);
            return view('pivots.calculate-workers-cost.objects.index', compact('infoByObjects', 'years', 'objects'));
        }

        $info = $this->calculateWorkersCostService->getPivotInfoByCompany($year);
        return view('pivots.calculate-workers-cost.company.index', compact('info', 'years', 'objects'));
    }
}
