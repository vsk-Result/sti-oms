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
        $requestData = array_merge(['cash_account_id' => [$cashAccount->id]], $request->toArray());
        $payments = $this->paymentService->filterPayments($requestData);

        return Excel::download(new Export($payments), 'Оплаты по кассе ' . $cashAccount->name . '.xlsx');
    }
}
