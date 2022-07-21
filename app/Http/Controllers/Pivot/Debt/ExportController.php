<?php

namespace App\Http\Controllers\Pivot\Debt;

use App\Exports\Pivot\Debt\Export;
use App\Http\Controllers\Controller;
use App\Services\DebtService;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class ExportController extends Controller
{
    private DebtService $debtService;

    public function __construct(DebtService $debtService)
    {
        $this->debtService = $debtService;
    }

    public function store(): BinaryFileResponse
    {
        $pivot = $this->debtService->getPivot();
        return Excel::download(new Export($pivot), 'Долги от СТИ.xlsx');
    }
}
