<?php

namespace App\Http\Controllers\Object\Report\WorkersCost;

use App\Http\Controllers\Controller;
use App\Models\Object\BObject;
use App\Services\Pivots\CalculateWorkersCost\CalculateWorkersCostService;
use Illuminate\Contracts\View\View;

class WorkersCostReportController extends Controller
{
    public function __construct(private CalculateWorkersCostService $calculateWorkersCostService) {}

    public function index(BObject $object): View
    {
        $infoByObjects = $this->calculateWorkersCostService->getPivotInfoByObjects([$object->id]);
        return view('objects.tabs.reports.workers_cost', compact('infoByObjects', 'object'));
    }
}
