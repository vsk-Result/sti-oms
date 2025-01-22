<?php

namespace App\Http\Controllers\API\Pivot\MoneyMovement;

use App\Exports\Pivot\MoneyMovement\Export;
use App\Http\Controllers\Controller;
use App\Services\ScheduleExportService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ExportController extends Controller
{
    public function __construct(private ScheduleExportService $scheduleExportService) {}

    public function store(Request $request): JsonResponse
    {
        if (! $request->has('verify_hash')) {
            return response()->json(['error' => 'Запрос не прошел валидацию'], 403);
        }

        if ($request->get('verify_hash') !== config('qr.verify_hash')) {
            return response()->json(['error' => 'Запрос не прошел валидацию'], 403);
        }

        $exportName = 'Отчет о движении денежных средств';
        $requestData = [
            'period' => $request->get('period', ''),
            'object_id' => $request->get('object_id', []),
            'bank_id' => $request->get('bank_id', []),
            'payment_type_id' => $request->get('payment_type_id', []),
            'organization_id' => $request->get('organization_id', []),
            'need_group_by_objects' => $request->has('need_group_by_objects')
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
            return response()->json(['status' => 'Отчет с данными параметрами находится на стадии формирования. После завершения на почту придет файл с отчетом.']);
        }

        $this->scheduleExportService->createTask(
            $exportName,
            Export::class,
            'pivot-money-movement',
            $exportName . '.xlsx',
            $requestData,
            $request->get('send_to_email', '')
        );

        return response()->json(['status' => 'Система начала формировать отчет. По завершению вам на почту придет файл с отчетом. Можете продолжить пользоваться сайтом.']);
    }
}
