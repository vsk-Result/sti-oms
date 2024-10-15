<?php

namespace App\Http\Controllers\Pivot\MoneyMovement;

use App\Exports\Pivot\MoneyMovement\Export;
use App\Http\Controllers\Controller;
use App\Services\ScheduleExportService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class ExportController extends Controller
{
    private ScheduleExportService $scheduleExportService;

    public function __construct(ScheduleExportService $scheduleExportService)
    {
        $this->scheduleExportService = $scheduleExportService;
    }

    public function store(Request $request): RedirectResponse
    {
        $exportName = 'Отчет о движении денежных средств';
        $requestData = [
            'period' => $request->get('period', ''),
            'object_id' => $request->get('object_id', []),
            'bank_id' => $request->get('bank_id', []),
            'organization_id' => $request->get('organization_id', [])
        ];

        $isTaskInReady = $this->scheduleExportService->isTaskReady(
            $exportName,
            $requestData
        );

        $isTaskInProgress = $this->scheduleExportService->isTaskInProgress(
            $exportName,
            $requestData
        );

        if ($isTaskInReady || $isTaskInProgress) {
            session()->flash('task_in_progress');

            return redirect()->back();
        }

        $this->scheduleExportService->createTask(
            $exportName,
            Export::class,
            'pivot-money-movement',
            $exportName . '.xlsx',
            $requestData
        );

        session()->flash('task_created');

        return redirect()->back()->withInput();
    }
}
