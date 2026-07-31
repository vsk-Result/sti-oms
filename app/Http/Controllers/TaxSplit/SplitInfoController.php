<?php

namespace App\Http\Controllers\TaxSplit;

use App\Http\Controllers\Controller;
use App\Services\SplitTaxInfoService;
use Illuminate\Http\JsonResponse;

class SplitInfoController extends Controller
{
    public function __construct(private SplitTaxInfoService $splitTaxInfoService) {}

    public function index(): JsonResponse
    {
        $splitInfo = $this->splitTaxInfoService->getSplitInfo();
        $splitInfoView = view('tax-split.split-info.index', ['info' => $splitInfo['info']])->render();

        return response()->json(
            [
                'split_info_view' => $splitInfoView,
                'status' => $splitInfo['status'],
                'message' => $splitInfo['message'],
            ]
        );
    }
}
