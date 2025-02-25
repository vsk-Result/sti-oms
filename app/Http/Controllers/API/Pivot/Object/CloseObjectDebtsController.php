<?php

namespace App\Http\Controllers\API\Pivot\Object;

use App\Http\Controllers\Controller;
use App\Models\Object\BObject;
use App\Models\PivotObjectDebt;
use App\Services\PivotObjectDebtService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CloseObjectDebtsController extends Controller
{
    public function __construct(private PivotObjectDebtService $pivotObjectDebtService) {}

    public function index(Request $request): JsonResponse
    {
        if (! $request->has('verify_hash')) {
            return response()->json(['error' => 'Запрос не прошел валидацию'], 403);
        }

        if ($request->get('verify_hash') !== config('qr.verify_hash')) {
            return response()->json(['error' => 'Запрос не прошел валидацию'], 403);
        }

        $object = BObject::where('code', '000')->first();
        if (! $object) {
            abort(404);
            return response()->json([], 404);
        }

        $contractorDebts = $this->pivotObjectDebtService->getPivotDebts($object->id, PivotObjectDebt::DEBT_TYPE_CONTRACTOR, ['with_sorted_details' => true]);
        $providerDebts = $this->pivotObjectDebtService->getPivotDebts($object->id, PivotObjectDebt::DEBT_TYPE_PROVIDER, ['with_sorted_details' => true]);
        $serviceDebts = $this->pivotObjectDebtService->getPivotDebts($object->id, PivotObjectDebt::DEBT_TYPE_SERVICE, ['with_sorted_details' => true]);

        $info = [
            'contractors' => $contractorDebts,
            'providers' => $providerDebts,
            'services' => $serviceDebts,
        ];

        return response()->json(compact('info'));
    }
}