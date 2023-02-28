<?php

namespace App\Http\Controllers\API\Debt;

use App\Http\Controllers\Controller;
use App\Models\Debt\Debt;
use App\Models\Debt\DebtImport;
use App\Models\Object\BObject;
use App\Models\Organization;
use App\Services\DebtService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

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

        if (isset($request->organization_id)) {
            $organization = Organization::find($request->organization_id);

            if (! $organization) {
                abort(404);
                return response()->json([], 404);
            }

            $pivot = $this->debtService->getPivot();

            $result = 0;
            $objects = [];

            foreach ($pivot['entries'] as $organizationId => $orgEntries) {
                if ($organizationId == $request->organization_id) {
                    foreach ($orgEntries as $objectId => $amount) {
                        $objects[BObject::find($objectId)->name] = $amount;
                        $result += $amount;
                    }
                }
            }

            $info = [
                'name' => $organization->name,
                'objects' => $objects,
                'result' => $result
            ];

            return response()->json(compact('info'));
        }

        $debtImport = DebtImport::where('type_id', DebtImport::TYPE_SUPPLY)->latest('date')->first();
        $debtDTImport = DebtImport::where('type_id', DebtImport::TYPE_DTTERMO)->latest('date')->first();
        $debt1CImport = DebtImport::where('type_id', DebtImport::TYPE_1C)->latest('date')->first();
        $debtObjectImport = DebtImport::where('type_id', DebtImport::TYPE_OBJECT)->latest('date')->first();

        $organization_ids = Debt::whereIn('import_id', [$debtImport?->id, $debtDTImport?->id, $debt1CImport?->id, $debtObjectImport?->id])
            ->groupBy('organization_id')
            ->pluck('organization_id')
            ->toArray();

        return response()->json(compact('organization_ids'));
    }
}
