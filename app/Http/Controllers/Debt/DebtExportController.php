<?php

namespace App\Http\Controllers\Debt;

use App\Exports\Debt\AllDebtsExport;
use App\Models\Object\BObject;
use App\Services\ScheduleExportService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class DebtExportController
{

    public function __construct(private ScheduleExportService $scheduleExportService)
    {}

    public function store(Request $request): RedirectResponse
    {
        $exportName = 'Отчет по долгам';
        $email = Auth()->user()->email;

        $status_id = $request->get('status', 0);
        $requestData = BObject::query()->where('status_id', $status_id)->whereNotIn('id', [137])->pluck('id')->toArray();

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
            AllDebtsExport::class,
            'exports/debts',
            $exportName . '.xlsx',
            $requestData,
            $email
        );

        session()->flash('task_created');

        return redirect()->back()->withInput();

    }
}