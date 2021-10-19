<?php

namespace App\Services\PaymentImport;

use App\Models\PaymentImport;
use App\Services\PaymentService;

class PaymentImportService
{
    private PaymentService $paymentService;

    public function __construct(PaymentService $paymentService) {
        $this->paymentService = $paymentService;
    }

    public function destroyImport(PaymentImport $import): PaymentImport
    {
        $import->delete();

        foreach ($import->payments as $payment) {
            $this->paymentService->destroyPayment($payment);
        }

        return $import;
    }
}
