<?php

namespace App\Http\Controllers\API\Debt;

use App\Http\Controllers\Controller;
use App\Models\Object\BObject;
use App\Models\Organization;
use App\Services\DebtService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class OrganizationController extends Controller
{
    private DebtService $debtService;

    public function __construct(DebtService $debtService)
    {
        $this->debtService = $debtService;
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

        $debtService = $this->debtService;

        $pivot = Cache::get('object_debts_pivot_1', function() use ($debtService) {
            return $debtService->getPivot();
        });

        if (isset($request->organization_name)) {
            $organization = Organization::where('name', $request->organization_name)->first();

            if (! $organization) {
                abort(404);
                return response()->json([], 404);
            }

            $result = [
                'amount' => 0,
                'guarantee' => 0,
            ];
            $objects = [];

            foreach ($pivot['entries'] as $organizationName => $orgEntries) {
                if ($organizationName == $request->organization_name) {
                    foreach ($orgEntries as $objectId => $amountInfo) {
                         $objName = BObject::find($objectId)->name;
                        if (isset($request->object_id)) {
                            if ($request->object_id === $objectId) {
                                $objects[$objName]['amount'] = $amountInfo['amount_without_guarantee'] + $amountInfo['avans'];
                                $objects[$objName]['guarantee'] = $amountInfo['guarantee'];
                                $result['amount'] += $amountInfo['amount_without_guarantee'] + $amountInfo['avans'];
                                $result['guarantee'] += $amountInfo['guarantee'];

                                break;
                            }

                            continue;
                        }

                        $objects[$objName]['amount'] = $amountInfo['amount_without_guarantee'] + $amountInfo['avans'];
                        $objects[$objName]['guarantee'] = $amountInfo['guarantee'];
                        $result['amount'] += $amountInfo['amount_without_guarantee'] + $amountInfo['avans'];
                        $result['guarantee'] += $amountInfo['guarantee'];
                    }

                    break;
                }
            }

            $info = [
                'name' => $organization->name,
                'objects' => $objects,
                'result' => $result,
            ];

            return response()->json(compact('info'));
        }

        $organization_names = $pivot['organizations'];

        return response()->json(compact('organization_names'));
    }
}
