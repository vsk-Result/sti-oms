<?php

namespace App\Http\Controllers\API\Finance;

use App\Http\Controllers\Controller;
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

        $years = ['2024', '2023', '2022', '2021'];
        $items = $this->generalReportService->getItems($years);

        $data = [];
        $total = [];
        foreach ($items as $categoryItem) {
            foreach ($categoryItem['codes']['receive'] as $receiveItem) {
                foreach ($receiveItem['years'] as $year => $yearAmount) {
                    if (! isset($data[$year][$categoryItem['name']]['receive'][$receiveItem['name']])) {
                        $data[$year][$categoryItem['name']]['receive'][$receiveItem['name']] = 0;
                    }

                    if (! isset($total[$year][$categoryItem['name']])) {
                        $total[$year][$categoryItem['name']] = 0;
                    }

                    if (! isset($total[$year]['total'])) {
                        $total[$year]['total'] = 0;
                    }

                    $data[$year][$categoryItem['name']]['receive'][$receiveItem['name']] += $yearAmount;
                    $total[$year][$categoryItem['name']] += $yearAmount;
                    $total[$year]['total'] += $yearAmount;
                }
            }

            foreach ($categoryItem['codes']['pay'] as $payItem) {
                foreach ($payItem['years'] as $year => $yearAmount) {
                    if (! isset($data[$year][$categoryItem['name']]['pay'][$payItem['name']])) {
                        $data[$year][$categoryItem['name']]['pay'][$payItem['name']] = 0;
                    }

                    if (! isset($total[$year][$categoryItem['name']])) {
                        $total[$year][$categoryItem['name']] = 0;
                    }

                    if (! isset($total[$year]['total'])) {
                        $total[$year]['total'] = 0;
                    }

                    $data[$year][$categoryItem['name']]['pay'][$payItem['name']] += $yearAmount;
                    $total[$year][$categoryItem['name']] += $yearAmount;
                    $total[$year]['total'] += $yearAmount;
                }
            }
        }

        $data['total'] = $total;

        return response()->json(compact('data'));
    }
}
