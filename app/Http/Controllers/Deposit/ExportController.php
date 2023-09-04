<?php

namespace App\Http\Controllers\Deposit;

use App\Exports\Deposit\Export;
use App\Http\Controllers\Controller;
use App\Services\DepositService;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class ExportController extends Controller
{
    private DepositService $depositService;

    public function __construct(DepositService $depositService)
    {
        $this->depositService = $depositService;
    }

    public function store(Request $request): BinaryFileResponse
    {
        $total = [];
        $deposits = $this->depositService->filterDeposit($request->toArray(), $total, false);

        return Excel::download(new Export($deposits), 'Экспорт депозитов.xlsx');
    }
}
