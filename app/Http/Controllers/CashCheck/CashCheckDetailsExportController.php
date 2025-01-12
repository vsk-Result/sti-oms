<?php

namespace App\Http\Controllers\CashCheck;

use App\Exports\CashCheck\Export;
use App\Http\Controllers\Controller;
use App\Models\CashCheck\CashCheck;
use App\Services\CashCheck\CashCheckService;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class CashCheckDetailsExportController extends Controller
{
    public function __construct(private CashCheckService $cashCheckService) {}

    public function store(CashCheck $check): BinaryFileResponse
    {
        $details = $this->cashCheckService->getCashCheckDetails($check);
        $title = 'Детализация кассы к закрытию ' . ($check->crmCost?->name ?? 'Не определено');

        return Excel::download(new Export($details, $title), $title . '.xlsx');
    }
}
