<?php

namespace App\Http\Controllers\Finance\GeneralReport;

use App\Exports\Finance\GeneralReport\Export;
use App\Http\Controllers\Controller;
use App\Services\GeneralReportService;
use Carbon\Carbon;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class ExportController extends Controller
{
    private GeneralReportService $generalReportService;

    public function __construct(GeneralReportService $generalReportService)
    {
        $this->generalReportService = $generalReportService;
    }

    public function store(): BinaryFileResponse
    {
        $years = ['2026', '2025', '2024', '2023', '2022', '2021'];
        $items = $this->generalReportService->getItems($years);

        return Excel::download(
            new Export($years, $items),
            'Отчет по общим затратам на ' . Carbon::now()->format('d-m-Y') . '.xlsx'
        );
    }
}
