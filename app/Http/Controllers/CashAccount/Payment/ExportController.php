<?php

namespace App\Http\Controllers\CashAccount\Payment;

use App\Exports\CashAccount\Payment\Export;
use App\Http\Controllers\Controller;
use App\Models\CashAccount\CashAccount;
use App\Services\CashAccount\Payment\PaymentService;
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

    public function store(Request $request, CashAccount $cashAccount): BinaryFileResponse
    {
        $payments = $this->paymentService->filterPayments($request->toArray());

        return Excel::download(new Export($payments), 'Оплаты по кассе ' . $cashAccount->name . '.xlsx');
    }
}
