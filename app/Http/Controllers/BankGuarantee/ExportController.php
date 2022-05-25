<?php

namespace App\Http\Controllers\BankGuarantee;

use App\Exports\BankGuarantee\Export;
use App\Http\Controllers\Controller;
use App\Services\BankGuaranteeService;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class ExportController extends Controller
{
    private BankGuaranteeService $guaranteeService;

    public function __construct(BankGuaranteeService $guaranteeService)
    {
        $this->guaranteeService = $guaranteeService;
    }

    public function store(Request $request): BinaryFileResponse
    {
        $total = [];
        $bankGuarantees = $this->guaranteeService->filterBankGuarantee($request->toArray(), $total, false);

        return Excel::download(new Export($bankGuarantees), 'Экспорт БГ и депозитов.xlsx');
    }
}
