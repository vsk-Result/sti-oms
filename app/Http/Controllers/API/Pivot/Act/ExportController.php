<?php

namespace App\Http\Controllers\API\Pivot\Act;

use App\Exports\Pivot\Act\Export;
use App\Http\Controllers\Controller;
use App\Services\Contract\ActService;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Maatwebsite\Excel\Facades\Excel;

class ExportController extends Controller
{
    private ActService $actService;

    public function __construct(ActService $actService)
    {
        $this->actService = $actService;
    }

    public function store(Request $request): JsonResponse
    {
        if (! $request->has('verify_hash')) {
            abort(403);
            return response()->json([], 403);
        }

        if ($request->get('verify_hash') !== config('qr.verify_hash')) {
            abort(403);
            return response()->json([], 403);
        }

        $pivot = $this->actService->getPivot();
        $filename = 'Долги к СТИ_' . Carbon::now()->format('d.m.Y') . '.xlsx';

        Excel::store(new Export($pivot), '/public/pivots/acts/' . $filename);

        $url = config('app.url') . '/storage/pivots/acts/' . $filename;

        return response()->json(['file' => [
            'name' => $filename,
            'url' => $url
        ]]);
    }
}
