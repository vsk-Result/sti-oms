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

        $pivot = Cache::get('object_debts_pivot', function() use ($debtService) {
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
                        if (isset($request->object_id)) {
                            if ($request->object_id === $objectId) {
                                $objects[BObject::find($objectId)->name] = $amountInfo['amount'];
                                $result['amount'] += $amountInfo['amount'];
                                $result['guarantee'] += $amountInfo['guarantee'];

                                break;
                            }

                            continue;
                        }

                        $objects[BObject::find($objectId)->name] = $amountInfo['amount'];
                        $result['amount'] += $amountInfo['amount'];
                        $result['guarantee'] += $amountInfo['guarantee'];
                    }

                    break;
                }
            }

            $result['amount'] = $result['amount'] - $result['guarantee'];

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
