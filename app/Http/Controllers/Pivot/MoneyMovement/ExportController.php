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
            'payment_type_id' => $request->get('payment_type_id', []),
            'organization_id' => $request->get('organization_id', []),
            'need_group_by_objects' => $request->has('need_group_by_objects')
        ];

        $email = auth()->user()->email;

        $isTaskInReady = $this->scheduleExportService->isTaskReady(
            $exportName,
            $requestData,
            $email
        );

        $isTaskInProgress = $this->scheduleExportService->isTaskInProgress(
            $exportName,
            $requestData,
            $email
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
            $requestData,
            $email
        );

        session()->flash('task_created');

        return redirect()->back()->withInput();
    }
}
