<?php

namespace App\Http\Controllers\Loan;

use App\Exports\Loan\Export;
use App\Http\Controllers\Controller;
use App\Services\LoanService;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class ExportController extends Controller
{
    private LoanService $loanService;

    public function __construct(LoanService $loanService)
    {
        $this->loanService = $loanService;
    }

    public function store(Request $request): BinaryFileResponse
    {
        $total = [];
        $loans = $this->loanService->filterLoans($request->toArray(), $total, false);

        return Excel::download(new Export($loans), 'Справка по займам и кредитам на ' . now()->format('d.m.Y') . '.xlsx');
    }
}
