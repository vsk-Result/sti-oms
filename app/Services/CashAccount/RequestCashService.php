<?php

namespace App\Services\CashAccount;

use App\Helpers\Sanitizer;
use App\Models\CashAccount\CashAccount;
use App\Models\CashAccount\CashAccountPayment;
use App\Models\Organization;
use App\Models\Payment;
use App\Services\CashAccount\Payment\PaymentService;

class RequestCashService
{
    public function __construct(
        private Sanitizer $sanitizer,
        private PaymentService $paymentService,
        private CashAccountService $cashAccountService
    ) {}

    public function requestCash(CashAccount $cashAccount, array $requestData)
    {
        $amount = abs($this->sanitizer->set($requestData['amount'])->toAmount()->get());

        $requestPayment = $this->paymentService->createPayment([
            'cash_account_id' => $cashAccount->id,
            'object_id' => null,
            'object_worktype_id' => null,
            'organization_id' => Organization::where('company_id', 1)->first()?->id ?? null,
            'type_id' => CashAccountPayment::TYPE_REQUEST,
            'category' => Payment::CATEGORY_TRANSFER,
            'code' => '',
            'description' => $requestData['description'],
            'date' => now(),
            'amount' => $amount,
            'status_id' => CashAccountPayment::STATUS_WAITING,
            'currency' => 'RUB',
            'currency_rate' => 1,
            'currency_amount' => $amount,
        ]);

        $transferPayment = $this->paymentService->createPayment([
            'cash_account_id' => $requestData['sender_id'],
            'object_id' => null,
            'object_worktype_id' => null,
            'organization_id' => Organization::where('company_id', 1)->first()?->id ?? null,
            'type_id' => CashAccountPayment::TYPE_TRANSFER,
            'category' => Payment::CATEGORY_TRANSFER,
            'code' => '',
            'description' => $requestData['description'],
            'date' => now(),
            'amount' => -$amount,
            'status_id' => CashAccountPayment::STATUS_WAITING,
            'currency' => 'RUB',
            'currency_rate' => 1,
            'currency_amount' => -$amount,
        ]);

        $requestPayment->update([
            'additional_data' => json_encode(['request_cash' => [
                'status_id' => CashAccountPayment::TRANSFER_STATUS_WAITING,
                'transfer_payment_id' => $transferPayment->id,
                'date_planned' => $requestData['date'],
                'is_initiator' => true
            ]])
        ]);

        $transferPayment->update([
            'additional_data' => json_encode(['transfer_cash' => [
                'status_id' => CashAccountPayment::TRANSFER_STATUS_WAITING,
                'request_payment_id' => $requestPayment->id,
                'date_planned' => $requestData['date'],
                'is_initiator' => false
            ]])
        ]);
    }

    public function updateRequest(CashAccount $cashAccount, CashAccountPayment $payment, array $requestData): void
    {
        $currentAdditionalData = json_decode($payment->additional_data, true) ?? [];
        $currentAdditionalData['request_cash']['status_id'] = $requestData['status_id'];

        $paymentStatusId = $requestData['status_id'] == CashAccountPayment::TRANSFER_STATUS_APPROVE
            ? CashAccountPayment::STATUS_ACTIVE
            : CashAccountPayment::STATUS_DELETED;

        $payment->update([
            'additional_data' => json_encode($currentAdditionalData),
            'status_id' => $paymentStatusId
        ]);

        $transferPayment = CashAccountPayment::find($currentAdditionalData['request_cash']['transfer_payment_id']);

        if ($transferPayment) {
            $currentAdditionalData = json_decode($transferPayment->additional_data, true) ?? [];
            $currentAdditionalData['transfer_cash']['status_id'] = $requestData['status_id'];

            $transferPayment->update([
                'additional_data' => json_encode($currentAdditionalData),
                'status_id' => $paymentStatusId
            ]);

            $this->cashAccountService->updateBalance($transferPayment->cashAccount);
        }
    }
}
