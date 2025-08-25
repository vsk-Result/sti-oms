<?php

namespace App\Http\Controllers\CashAccount\Payment;

use App\Http\Controllers\Controller;
use App\Models\CashAccount\CashAccount;
use App\Models\CashAccount\CashAccountPayment;
use App\Services\CashAccount\Payment\PaymentValidateService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PaymentValidateController extends Controller
{
    private PaymentValidateService $paymentValidateService;

    public function __construct(PaymentValidateService $paymentValidateService)
    {
        $this->paymentValidateService = $paymentValidateService;
    }

    public function update(CashAccount $cashAccount, CashAccountPayment $payment, Request $request): JsonResponse
    {
        $this->paymentValidateService->validate($payment, $request->all());
        return response()->json(['status' => 'success']);
    }
}
