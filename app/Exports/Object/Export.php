<?php

namespace App\Exports\Object;

use App\Exports\Object\Sheets\PaymentSheet;
use App\Models\Object\BObject;
use App\Models\Payment;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class Export implements WithMultipleSheets
{
    private BObject $object;

    public function __construct(BObject $object)
    {
        $this->object = $object;
    }

    public function sheets(): array
    {
        $paymentsQuery = Payment::where('object_id', $this->object->id)
            ->with('company', 'import', 'createdBy', 'object', 'audits', 'organizationSender', 'organizationReceiver')
            ->orderByDesc('date')
            ->orderByDesc('id');

        return [
            new PaymentSheet('Оплаты', $paymentsQuery, (clone $paymentsQuery)->count()),
            new PaymentSheet('Касса', (clone $paymentsQuery)->where('payment_type_id', Payment::PAYMENT_TYPE_CASH), (clone $paymentsQuery)->where('payment_type_id', Payment::PAYMENT_TYPE_CASH)->count()),
        ];
    }
}
