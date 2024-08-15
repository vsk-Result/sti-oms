<?php

namespace App\Http\Controllers\API\Object;

use App\Http\Controllers\Controller;
use App\Models\Debt\Debt;
use App\Models\Debt\DebtImport;
use App\Models\Object\BObject;
use App\Services\PivotObjectDebtService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class DebtController extends Controller
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

        $debtObjectImport = DebtImport::where('type_id', DebtImport::TYPE_OBJECT)->latest('date')->first();
        $objectExistInObjectImport = $debtObjectImport->debts()->where('object_id', $object->id)->count() > 0;
        $ds = Debt::where('import_id', $debtObjectImport->id)->where('type_id', Debt::TYPE_CONTRACTOR)->where('object_id', $object->id)->get();

        $contractors = [];
        $contractorsGU = [];
        foreach ($debts['contractor']->debts as $organization => $amount) {
            $organizationId = substr($organization, 0, strpos($organization, '::'));
            $guAmount = 0;
            $resultAmount = $amount;

            if ($objectExistInObjectImport) {
                $guAmount += $ds->where('organization_id', $organizationId)->sum('guarantee');
                $resultAmount += $ds->where('organization_id', $organizationId)->sum('avans');
            }

            if ($guAmount > 1 || $guAmount < -1) {
                $contractorsGU[$organization] = $guAmount;
            }
            if ($resultAmount > 1 || $resultAmount < -1) {
                $contractors[$organization] = $resultAmount;
            }
        }

        $fixProviders = [];
        foreach ($debts['provider']->debts_fix as $debt) {
            $id = $debt->organization_id . '::' . $debt->organization->name;

            if (! isset($fixProviders[$id])) {
                $fixProviders[$id] = 0;
            }

            $fixProviders[$id] += $debt->amount;
        }

        $floatProviders = [];
        foreach ($debts['provider']->debts_float as $debt) {
            $id = $debt->organization_id . '::' . $debt->organization->name;

            if (! isset($floatProviders[$id])) {
                $floatProviders[$id] = 0;
            }

            $floatProviders[$id] += $debt->amount;
        }

        asort($fixProviders);
        asort($floatProviders);

        $info = [
            'object' => $object->__toString(),
            'contractors' => $contractors,
            'contractors_gu' => $contractorsGU,
            'providers' => $debts['provider']->debts,
            'providers_fix' => $fixProviders,
            'providers_float' => $floatProviders,
            'service' => $debts['service']->debts,
        ];

        return response()->json(compact('info'));
    }
}
