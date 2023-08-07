<?php

namespace App\Http\Controllers\API\Object;

use App\Http\Controllers\Controller;
use App\Models\Debt\Debt;
use App\Models\Debt\DebtImport;
use App\Models\Object\BObject;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class DebtController extends Controller
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

        if (empty($request->object_id)) {
            abort(404);
            return response()->json([], 404);
        }

        $object = BObject::find($request->object_id);

        if (! $object) {
            abort(404);
            return response()->json([], 404);
        }

        $info = [
            'object' => $object->__toString(),
            'contractors' => $object->getContractorDebts(true),
            'providers' => $object->getProviderDebts(),
        ];

        return response()->json(compact('info'));
    }
}
