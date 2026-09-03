<?php

namespace App\Http\Controllers\Payment;

use App\Helpers\Sanitizer;
use App\Http\Controllers\Controller;
use App\Models\Payment;
use App\Services\PaymentService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class SplitByCategoryController extends Controller
{
    private PaymentService $paymentService;
    private Sanitizer $sanitizer;

    public function __construct(PaymentService $paymentService, Sanitizer $sanitizer)
    {
        $this->paymentService = $paymentService;
        $this->sanitizer = $sanitizer;
    }

    public function create(Payment $payment, Request $request): JsonResponse
    {
        return response()->json([
            'status' => 'success',
            'payment_form' => view('payments.parts._split_payment_form', compact('payment',))->render()
        ]);
    }

    public function store(Payment $payment, Request $request): RedirectResponse
    {
        $radRequestData = $payment->attributesToArray();
        $materialRequestData = $payment->attributesToArray();
        $opsteRequestData = $payment->attributesToArray();

        $radRequestData['category'] = Payment::CATEGORY_RAD;
        $materialRequestData['category'] = Payment::CATEGORY_MATERIAL;
        $opsteRequestData['category'] = Payment::CATEGORY_OPSTE;

        $radAmount = $this->sanitizer->set($request->get('split_amount_rad'))->toAmount()->get();
        $materialAmount = $this->sanitizer->set($request->get('split_amount_material'))->toAmount()->get();
        $opsteAmount = $this->sanitizer->set($request->get('split_amount_opste'))->toAmount()->get();

        $radNds = $payment->amount !== $payment->amount_without_nds ? round($radAmount / 6, 2) : 0;
        $materialNds = $payment->amount !== $payment->amount_without_nds ? round($materialAmount / 6, 2) : 0;
        $opsteNds = $payment->amount !== $payment->amount_without_nds ? round($opsteAmount / 6, 2) : 0;

        $radRequestData['amount'] = $radAmount;
        $radRequestData['amount_without_nds'] = $radAmount - $radNds;
        $radRequestData['was_split'] = true;

        $materialRequestData['amount'] = $materialAmount;
        $materialRequestData['amount_without_nds'] = $materialAmount - $materialNds;
        $materialRequestData['was_split'] = true;

        $opsteRequestData['amount'] = $opsteAmount;
        $opsteRequestData['amount_without_nds'] = $opsteAmount - $opsteNds;
        $opsteRequestData['was_split'] = true;

        $this->paymentService->createPayment($radRequestData);
        $this->paymentService->createPayment($materialRequestData);
        $this->paymentService->createPayment($opsteRequestData);

        $import = $payment->import;
        $this->paymentService->destroyPayment($payment);

        if ($import) {
            $import->reCalculateAmountsAndCounts();
        }

        return redirect()->back();
    }
}
