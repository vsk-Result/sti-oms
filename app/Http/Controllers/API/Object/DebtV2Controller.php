<?php

namespace App\Http\Controllers\API\Object;

use App\Http\Controllers\Controller;
use App\Models\Object\BObject;
use App\Models\PivotObjectDebt;
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

        $contractorDebts = $this->pivotObjectDebtService->getPivotDebts($object->id, PivotObjectDebt::DEBT_TYPE_CONTRACTOR);
        $providerDebts = $this->pivotObjectDebtService->getPivotDebts($object->id, PivotObjectDebt::DEBT_TYPE_PROVIDER);
        $serviceDebts = $this->pivotObjectDebtService->getPivotDebts($object->id, PivotObjectDebt::DEBT_TYPE_SERVICE);

        $salaryAmount = $object->getWorkSalaryDebt();

        $info = [
            'object' => $object->__toString(),
            'contractors_amount' => $contractorDebts['total']['total_amount'],
            'providers_amount' => $providerDebts['total']['amount'],
            'service_amount' => $serviceDebts['total']['total_amount'],
            'salary_amount' => $salaryAmount
        ];

        return response()->json(compact('info'));
    }
}
