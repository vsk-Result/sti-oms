<?php

namespace App\Http\Controllers\Object\Report\WorkersCost;

use App\Exports\Pivot\CalculateWorkersCost\ExportByObject;
use App\Http\Controllers\Controller;
use App\Models\Object\BObject;
use App\Services\Pivots\CalculateWorkersCost\CalculateWorkersCostService;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class WorkersCostReportExportController extends Controller
{
    public function __construct(private CalculateWorkersCostService $calculateWorkersCostService) {}

    public function store(BObject $object): BinaryFileResponse
    {
        $infoByObjects = $this->calculateWorkersCostService->getPivotInfoByObjects(date('Y'), [$object->id]);

        return Excel::download(
            new ExportByObject($infoByObjects, date('Y')),
            'Расчет стоимости рабочих по объекту ' . $object->getName() . '.xlsx'
        );
    }
}
