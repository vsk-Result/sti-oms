<?php

namespace App\Http\Controllers\Payment;

use App\Http\Controllers\Controller;
use App\Services\PaymentService;
use Illuminate\Http\RedirectResponse;

class NDSController extends Controller
{
    private PaymentService $paymentService;

    public function __construct(PaymentService $paymentService)
    {
        $this->paymentService = $paymentService;
    }

    public function store(): RedirectResponse
    {
        $this->paymentService->determiningNDSPayments();
        return redirect()->back();
    }
}
