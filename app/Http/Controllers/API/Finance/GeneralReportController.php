<?php

namespace App\Http\Controllers\API\Finance;

use App\Http\Controllers\Controller;
use App\Models\CurrencyExchangeRate;
use App\Models\FinanceReportHistory;
use App\Services\GeneralReportService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class GeneralReportController extends Controller
{
    private GeneralReportService $generalReportService;

    public function __construct(GeneralReportService $generalReportService)
    {
        $this->generalReportService = $generalReportService;
    }

    public function index(Request $request): JsonResponse
    {
//        if (! $request->has('verify_hash')) {
//            return response()->json(['error' => 'Запрос не прошел валидацию'], 403);
//        }
//
//        if ($request->get('verify_hash') !== config('qr.verify_hash')) {
//            return response()->json(['error' => 'Запрос не прошел валидацию'], 403);
//        }

        if (! $request->has('year')) {
            return response()->json(['error' => 'Отсутствует year'], 403);
        }

        $year = $request->get('year');
        $years = ['2024', '2023', '2022', '2021'];

        if ($year !== 'all') {
            $years = [$year];
        }

        $data = [
            'year' => $year,
            'categories' => []
        ];

        $items = $this->generalReportService->getItems($years);

        foreach ($items as $item) {
            $category = [
                'name' => $item['name'],
                'pay' => [],
                'receive' => []
            ];

        }

        dd($items);

        return response()->json(compact('data'));
    }
}
