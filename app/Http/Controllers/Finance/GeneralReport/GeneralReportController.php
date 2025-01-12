<?php

namespace App\Http\Controllers\Finance\GeneralReport;

use App\Http\Controllers\Controller;
use App\Services\GeneralReportService;
use Illuminate\View\View;

class GeneralReportController extends Controller
{
    private GeneralReportService $generalReportService;

    public function __construct(GeneralReportService $generalReportService)
    {
        $this->generalReportService = $generalReportService;
    }

    public function index(): View
    {
        $years = ['2025', '2024', '2023', '2022', '2021'];
        $items = $this->generalReportService->getItems($years);
        return view('finance.general-report.index', compact('years', 'items'));
    }
}
