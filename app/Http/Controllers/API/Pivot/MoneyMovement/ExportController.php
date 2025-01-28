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

        if (mb_strlen(trim($request->get('period', ''))) === 0) {
            return response()->json(['error' => 'Период не должен быть пустым'], 404);
        }

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

        $email = $request->get('send_to_email', '');

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
            return response()->json(['status' => 'Отчет с данными параметрами находится на стадии формирования. После завершения на почту придет файл с отчетом.']);
        }

        $this->scheduleExportService->createTask(
            $exportName,
            Export::class,
            'pivot-money-movement',
            $exportName . '.xlsx',
            $requestData,
            $email
        );

        return response()->json(['status' => 'Система начала формировать отчет. По завершению вам на почту придет файл с отчетом. Можете продолжить пользоваться сайтом.']);
    }
}
