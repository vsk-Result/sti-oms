<?php

namespace App\Http\Controllers\Pivot\CalculateWorkersCost;

use App\Http\Controllers\Controller;
use App\Services\Pivots\CalculateWorkersCost\CalculateWorkersCostService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CalculateWorkersCostController extends Controller
{
    public function __construct(private CalculateWorkersCostService $calculateWorkersCostService) {}

    public function index(Request $request): View
    {
        $info = $this->calculateWorkersCostService->getPivotInfo();
        return view('pivots.calculate-workers-cost.index', compact('info'));
    }
}
