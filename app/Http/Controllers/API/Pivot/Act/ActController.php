<?php

namespace App\Http\Controllers\API\Pivot\Act;

use App\Http\Controllers\Controller;
use App\Models\Object\BObject;
use App\Services\Contract\ActService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ActController extends Controller
{
    private ActService $actService;

    public function __construct(ActService $actService)
    {
        $this->actService = $actService;
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

        if (! $request->has('access_objects')) {
            return response()->json(['error' => 'Отсутствует access_objects'], 403);
        }

        if ($request->has('object_id')) {
            return response()->json(['pivot' => $this->actService->getPivot($request->object_id)]);
        }

        $accessObjects = null;
        if ($request->get('access_objects') !== '*') {
            $accessObjects = explode(',', $request->get('access_objects'));
        }

        $pivot = $this->actService->getPivot(null, $accessObjects);

        return response()->json(compact('pivot'));
    }
}
