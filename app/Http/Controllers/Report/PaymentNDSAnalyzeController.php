<?php

namespace App\Http\Controllers\Report;

use App\Exports\Payment\Export;
use App\Http\Controllers\Controller;
use App\Models\Organization;
use App\Models\Payment;
use Maatwebsite\Excel\Facades\Excel;

class PaymentNDSAnalyzeController extends Controller
{
    public function store()
    {
        $strangePayOrganizations = [];
        $strangeReceiveOrganizations = [];
        Payment::where('payment_type_id', Payment::PAYMENT_TYPE_NON_CASH)->where('type_id', Payment::TYPE_OBJECT)->chunk(1000, function ($payments) use (&$strangePayOrganizations, &$strangeReceiveOrganizations) {
            foreach ($payments as $payment) {
                if ($payment->amount !== $payment->amount_without_nds) {
                    if ($payment->amount > 0) {
                        $strangeReceiveOrganizations[] = $payment->organization_sender_id;
                    } else {
                        $strangePayOrganizations[] = $payment->organization_receiver_id;
                    }
                }
            }
        });
        $strangePayOrganizations = array_unique($strangePayOrganizations);
        $strangeReceiveOrganizations = array_unique($strangeReceiveOrganizations);

        $strangePayments = [];
        foreach (Organization::whereIn('id', $strangePayOrganizations)->with(['paymentsSend' => function($q) {
            $q->where('payment_type_id', Payment::PAYMENT_TYPE_NON_CASH)->where('type_id', Payment::TYPE_OBJECT);
        }])->get() as $organization) {
            foreach ($organization->paymentsSend as $payment) {
                if ($payment->amount === $payment->amount_without_nds) {
                    $strangePayments[] = $payment->id;
                }
            }
        }

        foreach (Organization::whereIn('id', $strangeReceiveOrganizations)->with(['paymentsReceive' => function($q) {
            $q->where('payment_type_id', Payment::PAYMENT_TYPE_NON_CASH)->where('type_id', Payment::TYPE_OBJECT);
        }])->get() as $organization) {
            foreach ($organization->paymentsReceive as $payment) {
                if ($payment->amount === $payment->amount_without_nds) {
                    $strangePayments[] = $payment->id;
                }
            }
        }

        $strangePayments = array_unique($strangePayments);
        $strangePayments = array_slice($strangePayments, 0, 500);

        $payments = Payment::whereIn('id', $strangePayments)
            ->with('company', 'organizationReceiver', 'organizationSender')
            ->orderBy('date');

        return Excel::download(new Export($payments), 'Анализ оплат на предмет вычета НДС.xlsx');
    }
}
