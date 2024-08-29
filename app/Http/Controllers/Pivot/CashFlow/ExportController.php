<?php

namespace App\Http\Controllers\Pivot\CashFlow;

use App\Exports\Pivot\CashFlow\Export;
use App\Http\Controllers\Controller;
use App\Services\ReceivePlanService;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class ExportController extends Controller
{
    private ReceivePlanService $receivePlanService;

    public function __construct(ReceivePlanService $receivePlanService)
    {
        $this->receivePlanService = $receivePlanService;
    }

    public function store(): BinaryFileResponse
    {
        return Excel::download(new Export($this->receivePlanService), 'Отчет CASH FLOW.xlsx');
    }
}
