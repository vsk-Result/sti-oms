<?php

namespace App\Http\Controllers\API\Finance;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class FinanceReportExportController extends Controller
{
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

        $fileName = 'Финансовый_отчет_' . now()->format('d_m_Y') . '.xlsx';

        $url = config('app.url') . '/storage/exports/finance/' . $fileName;

        return response()->json(['file' => [
            'name' => $fileName,
            'url' => $url
        ]]);
    }
}
