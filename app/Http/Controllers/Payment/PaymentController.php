<?php

namespace App\Http\Controllers\Payment;

use App\Http\Controllers\Controller;
use App\Http\Requests\Payment\StorePaymentRequest;
use App\Models\Payment;
use App\Services\PaymentService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    private PaymentService $paymentService;

    public function __construct(PaymentService $paymentService)
    {
        $this->paymentService = $paymentService;
    }

    public function index()
    {
        $payments = Payment::with('company', 'createdBy')->orderByDesc('date')->orderByDesc('id')->get();
        return view('payments.index', compact('payments'));
    }

    public function store(Request $request): JsonResponse
    {
        $payment = $this->paymentService->createPayment($request->toArray());
        $payment->statement?->reCalculateAmountsAndCounts();

        return response()->json([
            'status' => 'success',
            'message' => 'Оплата успешно создана',
            'payment' => [
                'id' => $payment->id,
                'update_url' => route('payments.update', $payment),
                'destroy_url' => route('payments.destroy', $payment),
            ]
        ]);
    }

    public function update(Payment $payment, Request $request): JsonResponse
    {
        $this->paymentService->updatePayment($payment, $request->toArray());
        $payment->statement?->reCalculateAmountsAndCounts();

        return response()->json([
            'status' => 'success',
            'message' => 'Оплата успешно изменена'
        ]);
    }

    public function destroy(Payment $payment): JsonResponse
    {
        $this->paymentService->destroyPayment($payment);
        $payment->statement?->reCalculateAmountsAndCounts();

        return response()->json([
            'status' => 'success',
            'message' => 'Оплата успешно удалена'
        ]);
    }
}
