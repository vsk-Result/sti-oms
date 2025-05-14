<?php

namespace App\Http\Controllers\Object\CashFlow;

use App\Http\Controllers\Controller;
use App\Models\Object\BObject;
use App\Models\Object\CashFlowPayment;
use App\Services\Object\CashFlow\CashFlowPaymentService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class CashFlowPaymentController extends Controller
{
    public function __construct(private CashFlowPaymentService $cashFlowPaymentService) { }

    public function store(BObject $object, Request $request): RedirectResponse
    {
        $requestDate = array_merge($request->toArray(), ['object_id' => $object->id]);
        $this->cashFlowPaymentService->createPayment($requestDate);

        return redirect()->back();
    }

    public function destroy(BObject $object, CashFlowPayment $payment): RedirectResponse
    {
        $this->cashFlowPaymentService->destroyPayment(['payment_id' => $payment->id]);
        return redirect()->back();
    }
}
