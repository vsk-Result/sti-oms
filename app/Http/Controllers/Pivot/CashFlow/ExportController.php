<?php

namespace App\Http\Controllers\Pivot\CashFlow;

use App\Exports\Pivot\CashFlow\Export;
use App\Http\Controllers\Controller;
use App\Services\FinanceReport\AccountBalanceService;
use App\Services\ReceivePlanService;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class ExportController extends Controller
{
    public function __construct(
        private ReceivePlanService $receivePlanService,
        private AccountBalanceService $accountBalanceService
    ) {}

    public function store(Request $request): BinaryFileResponse
    {
        return Excel::download(new Export(
            $this->receivePlanService,
            $this->accountBalanceService,
            $request->all()
        ), 'Отчет CASH FLOW.xlsx');
    }
}
