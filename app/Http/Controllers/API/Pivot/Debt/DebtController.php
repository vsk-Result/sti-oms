<?php

namespace App\Http\Controllers\API\Pivot\Debt;

use App\Http\Controllers\Controller;
use App\Models\Object\BObject;
use App\Services\DebtService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class DebtController extends Controller
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

        if (empty($request->object_id)) {
            $objectList = BObject::active()->orderBy('code')->get();
            $objects = [];

            foreach ($objectList as $object) {
                $objects[$object->id] = $object->getName();
            }

            return response()->json(compact('objects'));
        }

        $pivot = $this->debtService->getPivot($request->object_id);

        return response()->json(compact('pivot'));
    }
}
