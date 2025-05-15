<?php

namespace App\Http\Controllers\Pivot\CalculateWorkersCost;

use App\Exports\Pivot\CalculateWorkersCost\Export;
use App\Http\Controllers\Controller;
use App\Services\Pivots\CalculateWorkersCost\CalculateWorkersCostService;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class CalculateWorkersCostControllerExportController extends Controller
{
    public function __construct(private CalculateWorkersCostService $calculateWorkersCostService) {}

    public function store(Request $request): BinaryFileResponse
    {
        $info = $this->calculateWorkersCostService->getPivotInfo();

        return Excel::download(
            new Export($info),
            'Расчет стоимости рабочих по компании.xlsx'
        );
    }
}
