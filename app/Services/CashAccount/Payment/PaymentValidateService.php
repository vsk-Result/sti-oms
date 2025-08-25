<?php

namespace App\Services\CashAccount\Payment;

use App\Models\CashAccount\CashAccount;
use App\Models\CashAccount\CashAccountPayment;
use Illuminate\Database\Eloquent\Collection;

class PaymentValidateService
{
    public function validate(CashAccountPayment $payment, array $requestData): void
    {
        $payment->update([
            'status_id' => $requestData['valid'] === 'true'
                ? CashAccountPayment::STATUS_VALID
                : CashAccountPayment::STATUS_ACTIVE
        ]);
    }

    public function getActiveValidPayments(CashAccount $cashAccount): Collection
    {
        return $cashAccount->payments()->where('status_id', CashAccountPayment::STATUS_VALID)->get();
    }
}
