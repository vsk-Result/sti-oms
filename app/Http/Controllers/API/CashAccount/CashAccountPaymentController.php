<?php

namespace App\Http\Controllers\API\CashAccount;

use App\Http\Controllers\Controller;
use App\Services\CashAccount\Payment\PaymentService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CashAccountPaymentController extends Controller
{
    public function __construct(
        private PaymentService $paymentService,
    ) {}

    public function index(Request $request): JsonResponse
    {
        if (! $request->has('verify_hash')) {
            return response()->json(['error' => 'Запрос не прошел валидацию'], 403);
        }

        if ($request->get('verify_hash') !== config('qr.verify_hash')) {
            return response()->json(['error' => 'Запрос не прошел валидацию'], 403);
        }

        if (! $request->has('cash_account_id')) {
            return response()->json(['error' => 'Отсутствует параметр cash_account_id'], 404);
        }

        $totalInfo = [];
        $payments = $this->paymentService->filterPayments(['cash_account_id' => [$request->get('cash_account_id')]], false, $totalInfo)->get();

        $data = [
            'payments' => [],
        ];

        foreach ($payments as $payment) {
            $type = $payment->getType();

            if (! is_null($payment->getCrmAvansData()['employee_id'])) {
                $type .= ' ' . '(CRM)';
            }

            if ($payment->isRequest()) {
                $type .= ' ' . $payment->getRequestStatus();
            }

            if ($payment->isTransfer()) {
                $type .= ' ' . $payment->getTransferStatus();
            }

            $p = [
                'cash_account_id' => $request->get('cash_account_id'),
                'type' => $type,
                'date' => $payment->getDateFormatted(),
                'object_id' => $payment->object_id,
                'object_code' => $payment->getObjectCode(),
                'code' => $payment->code,
                'organization' => $payment->organization?->name,
                'description' => $payment->getDescription(),
                'amount' => $payment->amount,
                'category' => $payment->category,
                'is_validated' => $payment->isValid(),
                'media' => [],
            ];

            foreach ($payment->getMedia() as $media) {
                $p['media'][] = [
                    'url' => $media->getUrl(),
                    'name' => $media->file_name . '      (' . $media->human_readable_size . ')'
                ];
            }


            $data['payments'][] = $p;
        }

        return response()->json(compact('data'));
    }
}
