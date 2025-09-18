<?php

namespace App\Http\Controllers\Pivot\CalculateWorkersCost;

use App\Exports\Pivot\CalculateWorkersCost\ExportByCompany;
use App\Exports\Pivot\CalculateWorkersCost\ExportByObject;
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
        $objectIds = $request->get('object_id', []);
        $year = $request->get('year', date('Y'));

        if (count($objectIds) > 0) {
            $infoByObjects = $this->calculateWorkersCostService->getPivotInfoByObjects($objectIds);

            return Excel::download(
                new ExportByObject($infoByObjects),
                'Расчет стоимости рабочих по объектам.xlsx'
            );
        }

        $info = $this->calculateWorkersCostService->getPivotInfoByCompany($year);

        return Excel::download(
            new ExportByCompany($info, $year),
            'Расчет стоимости рабочих по компании за ' . $year . ' год.xlsx'
        );
    }
}
