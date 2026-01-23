<?php

namespace App\Http\Controllers\Pivot\MoneyMovement;

use App\Exports\Pivot\MoneyMovement\Export;
use App\Http\Controllers\Controller;
use App\Models\Object\BObject;
use App\Services\ScheduleExportService;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class ExportController extends Controller
{
    private ScheduleExportService $scheduleExportService;

    public function __construct(ScheduleExportService $scheduleExportService)
    {
        $this->scheduleExportService = $scheduleExportService;
    }

    public function store(Request $request): RedirectResponse | BinaryFileResponse
    {
        $periodSplit = explode(' - ', $request->get('period'));
        $periodDiff = Carbon::parse($periodSplit[0])->diffInDays($periodSplit[1]);
        $exportName = 'Отчет о движении денежных средств';

        $requestData = [
            'period' => $request->get('period', ''),
            'object_id' => $request->get('object_id', []),
            'bank_id' => $request->get('bank_id', []),
            'payment_type_id' => $request->get('payment_type_id', []),
            'organization_id' => $request->get('organization_id', []),
            'need_group_by_objects' => $request->has('need_group_by_objects'),
            'need_transfers' => $request->has('need_transfers')
        ];

        if ($periodDiff <= 180) {
            return Excel::download(new Export($requestData), $exportName . '.xlsx');
        }

        if (count($requestData['object_id']) === 0) {
            if (auth()->user()->hasRole(['object-leader', 'finance-object-user'])) {
                $requestData['object_id'] = auth()->user()->objects->pluck('id')->toArray();
            }
        }

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
