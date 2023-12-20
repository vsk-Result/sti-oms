<?php

namespace App\Http\Controllers\API\Object;

use App\Http\Controllers\Controller;
use App\Models\CurrencyExchangeRate;
use App\Models\Debt\Debt;
use App\Models\Debt\DebtImport;
use App\Models\Object\BObject;
use App\Services\PivotObjectDebtService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class DebtV2Controller extends Controller
{
    private PivotObjectDebtService $pivotObjectDebtService;

    public function __construct(PivotObjectDebtService $pivotObjectDebtService)
    {
        $this->pivotObjectDebtService = $pivotObjectDebtService;
    }

    public function index(Request $request): JsonResponse
    {
        if (! $request->has('verify_hash')) {
            abort(403);
            return response()->json([], 403);
        }

        if ($request->get('verify_hash') !== config('qr.verify_hash')) {
            abort(403);
            return response()->json([], 403);
        }

        if (empty($request->object_id)) {
            abort(404);
            return response()->json([], 404);
        }

        $object = BObject::find($request->object_id);

        if (! $object) {
            abort(404);
            return response()->json([], 404);
        }

        $debts = $this->pivotObjectDebtService->getPivotDebtForObject($object->id);

        $info = [
            'object' => $object->__toString(),
            'contractors_amount' => CurrencyExchangeRate::format($debts['contractor']->total_amount, 'RUB'),
            'providers_amount' => CurrencyExchangeRate::format($debts['provider']->total_amount, 'RUB'),
            'service_amount' => CurrencyExchangeRate::format($debts['service']->total_amount, 'RUB'),
        ];

        return response()->json(compact('info'));
    }
}
