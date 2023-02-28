<?php

namespace App\Http\Controllers\API\Debt;

use App\Http\Controllers\Controller;
use App\Models\Debt\Debt;
use App\Models\Debt\DebtImport;
use App\Models\Object\BObject;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class OrganizationController extends Controller
{
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
