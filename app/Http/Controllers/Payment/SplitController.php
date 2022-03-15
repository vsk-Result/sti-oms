<?php

namespace App\Http\Controllers\Payment;

use App\Http\Controllers\Controller;
use App\Models\Object\BObject;
use App\Models\Payment;
use App\Services\PaymentService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SplitController extends Controller
{
    private PaymentService $paymentService;

    public function __construct(PaymentService $paymentService)
    {
        $this->paymentService = $paymentService;
    }

    public function store(Payment $payment, Request $request): JsonResponse
    {
        $payments = $this->paymentService->splitPayment($payment, $request->toArray());

        $viewRender = [];
        $isNewPayment = true;
        $categories = Payment::getCategories();
        $objects = Payment::getTypes() + BObject::getObjectsList();

        foreach ($payments as $payment) {
            $viewRender[] = view(
                'payment-imports.partials._edit_payment_table_row',
                compact('payment', 'categories', 'objects', 'isNewPayment')
            )->render();
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Оплата успешно разбита',
            'view_render' => $viewRender
        ]);
    }
}
