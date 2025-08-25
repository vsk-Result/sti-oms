<?php

namespace App\Services\CashAccount\Payment;

use App\Models\CashAccount\CashAccountPayment;
use App\Models\Payment;
use App\Models\Status;
use App\Services\OrganizationService;
use App\Services\PaymentService;
use App\Services\CashAccount\Payment\PaymentService as CashAccountPaymentService;

class PaymentTransferToObjectService
{
    public function __construct(
        private OrganizationService $organizationService,
        private PaymentService $paymentService,
        private CashAccountPaymentService $cashAccountPaymentService,
    ) {}

    public function transfer(CashAccountPayment $payment): void
    {
        $companyOrganization = $this->organizationService->getOrCreateOrganization([
            'company_id' => 1,
            'name' => 'ООО "Строй Техно Инженеринг"',
            'inn' => '7720734368',
            'kpp' => null
        ]);

        $objectPayment = $this->paymentService->createPayment([
            'company_id' => 1,
            'bank_id' => null,
            'object_id' => $payment->object_id,
            'object_worktype_id' => $payment->object_worktype_id,
            'organization_sender_id' => $payment->amount > 0 ? $payment->organization_id : $companyOrganization->id,
            'organization_receiver_id' => $payment->amount > 0 ? $companyOrganization->id : $payment->organization_id,
            'type_id' => Payment::TYPE_OBJECT,
            'payment_type_id' => Payment::PAYMENT_TYPE_CASH,
            'category' => $payment->category,
            'code' => $payment->code,
            'description' => $payment->getDescription(),
            'date' => $payment->date,
            'amount' => $payment->amount,
            'amount_without_nds' => $payment->amount,
            'status_id' => Status::STATUS_ACTIVE,
            'currency_amount' => $payment->amount,
        ]);

        $this->cashAccountPaymentService->createObjectPayment($payment, [
            'object_payment_id' => $objectPayment->id
        ]);
    }
}
