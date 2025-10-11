<?php

namespace App\Http\Controllers\API\Object;

use App\Http\Controllers\Controller;
use App\Models\Object\BObject;
use App\Models\PivotObjectDebt;
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

        $contractorDebts = $this->pivotObjectDebtService->getPivotDebts($object->id, PivotObjectDebt::DEBT_TYPE_CONTRACTOR);
        $providerDebts = $this->pivotObjectDebtService->getPivotDebts($object->id, PivotObjectDebt::DEBT_TYPE_PROVIDER);
        $serviceDebts = $this->pivotObjectDebtService->getPivotDebts($object->id, PivotObjectDebt::DEBT_TYPE_SERVICE);

        $contractors = [];
        $contractorsGU = [];
        foreach ($contractorDebts['organizations'] as $organizationData) {
            if (is_valid_amount_in_range($organizationData['guarantee'])) {
                $contractorsGU[$organizationData['organization_name']] = $organizationData['guarantee'];
            }

            if (is_valid_amount_in_range($organizationData['total_amount'])) {
                $contractors[$organizationData['organization_name']] = $organizationData['total_amount'];
            }
        }

        $providers = [];
        $fixProviders = [];
        $floatProviders = [];
        foreach ($providerDebts['organizations'] as $organizationData) {
            if (is_valid_amount_in_range($organizationData['amount'])) {
                if (! isset($providers[$organizationData['organization_name']])) {
                    $providers[$organizationData['organization_name']] = 0;
                }

                $providers[$organizationData['organization_name']] += $organizationData['amount'];
            }

            if (is_valid_amount_in_range($organizationData['amount_fix'])) {
                if (! isset($fixProviders[$organizationData['organization_name']])) {
                    $fixProviders[$organizationData['organization_name']] = 0;
                }

                $fixProviders[$organizationData['organization_name']] += $organizationData['amount_fix'];
            }

            if (is_valid_amount_in_range($organizationData['amount_float'])) {
                if (! isset($floatProviders[$organizationData['organization_name']])) {
                    $floatProviders[$organizationData['organization_name']] = 0;
                }

                $floatProviders[$organizationData['organization_name']] += $organizationData['amount_float'];
            }
        }

        $services = [];
        foreach ($serviceDebts['organizations'] as $organizationData) {
            if (is_valid_amount_in_range($organizationData['total_amount'])) {
                $services[$organizationData['organization_name']] = $organizationData['total_amount'];
            }
        }

        asort($contractors);
        asort($contractorsGU);
        asort($providers);
        asort($fixProviders);
        asort($floatProviders);
        asort($services);

        $info = [
            'object' => $object->__toString(),
            'contractors' => $contractors,
            'contractors_gu' => $contractorsGU,
            'providers' => $providers,
            'providers_fix' => $fixProviders,
            'providers_float' => $floatProviders,
            'service' => $services,
        ];

        return response()->json(compact('info'));
    }
}
