<?php

namespace App\Http\Controllers\Payment;

use App\Exports\PaymentImport\Export;
use App\Http\Controllers\Controller;
use App\Services\PaymentService;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class ExportController extends Controller
{
    private PaymentService $paymentService;

    public function __construct(PaymentService $paymentService)
    {
        $this->paymentService = $paymentService;
    }

    public function store(Request $request): BinaryFileResponse
    {
        $payments = $this->paymentService->filterPayments($request->toArray());

        return Excel::download(new Export($payments), 'Экспорт оплат.xlsx');
    }
}
