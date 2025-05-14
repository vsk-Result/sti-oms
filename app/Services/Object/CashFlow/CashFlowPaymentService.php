<?php

namespace App\Services\Object\CashFlow;

use App\Helpers\Sanitizer;
use App\Models\Object\CashFlowPayment;
use App\Models\Status;

class CashFlowPaymentService
{
    public function __construct(private Sanitizer $sanitizer) { }

    public function createPayment(array $requestData): CashFlowPayment
    {
        return CashFlowPayment::create([
            'category_id' => $requestData['category_id'] ?? null,
            'object_id' => $requestData['object_id'] ?? null,
            'organization_id' => $requestData['organization_id'] ?? null,
            'amount' => -abs($this->sanitizer->set($requestData['amount'])->toAmount()->get()),
            'date' => $requestData['date'],
            'status_id' => Status::STATUS_ACTIVE,
        ]);
    }

    public function destroyPayment(array $requestData): void
    {
        $payment = $this->findPayment($requestData['payment_id']);
        $payment->delete();
    }

    public function findPayment($id): CashFlowPayment | null
    {
        return CashFlowPayment::find($id);
    }
}
