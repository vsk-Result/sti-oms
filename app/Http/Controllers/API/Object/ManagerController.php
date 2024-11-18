<?php

namespace App\Http\Controllers\API\Object;

use App\Http\Controllers\Controller;
use App\Models\Contract\Contract;
use App\Models\CurrencyExchangeRate;
use App\Models\FinanceReportHistory;
use App\Models\Object\BObject;
use App\Services\ManagerObjectService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ManagerController extends Controller
{
    public function __construct(private ManagerObjectService $managerObjectService) {}

    public function index(Request $request): JsonResponse
    {
        if (! $request->has('verify_hash')) {
            return response()->json(['error' => 'Запрос не прошел валидацию'], 403);
        }

        if ($request->get('verify_hash') !== config('qr.verify_hash')) {
            return response()->json(['error' => 'Запрос не прошел валидацию'], 403);
        }

        $info = [
            'managers' => $this->managerObjectService->getManagers(),
            'export_filename' => 'managers_objects.xlsx',
            'export_path' => config('app.url') . '/storage/public/objects-debts-manuals/managers_objects.xlsx',
        ];

        return response()->json(compact('info'));
    }
}
