<?php

namespace App\Http\Controllers\Object\Report\PivotPayments;

use App\Exports\Object\PivotPayments\Export;
use App\Http\Controllers\Controller;
use App\Models\FinanceReportHistory;
use App\Models\Object\BObject;
use App\Services\PivotObjectDebtService;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class ExportController extends Controller
{
    public function __construct(
        private PivotObjectDebtService $pivotObjectDebtService
    ) {}

    public function store(BObject $object): BinaryFileResponse
    {
        $lastDate = FinanceReportHistory::select('date')->latest('date')->first()->date ?? now()->format('Y-m-d');
        $financeReportHistory = FinanceReportHistory::where('date', $lastDate)->first();

        $objectsInfo = json_decode($financeReportHistory->objects_new);
        $years = collect($objectsInfo->years)->toArray();
        $total = $objectsInfo->total;

        $info = [];

        foreach ($years as $year => $objects) {
            foreach ($objects as $o) {
                if ($o->id === $object->id) {
                    $info = (array) $total->{$year}->{$object->code};
                    break;
                }
            }
        }

        return Excel::download(
            new Export($object, [
                'pivotObjectDebtService' => $this->pivotObjectDebtService,
                'pivotInfo' => $info
            ]),
            'Сводный отчет по расходам на ' . now()->format('d.m.Y') . ' по объекту ' . $object->getName() . '.xlsx'
        );
    }
}
