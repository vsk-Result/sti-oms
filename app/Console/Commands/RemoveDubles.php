<?php

namespace App\Console\Commands;

use App\Console\HandledCommand;
use App\Models\CashAccount\CashAccountPayment;
use App\Models\CRM\CostItem;
use App\Models\KostCode;
use App\Models\Payment;

class RemoveDubles extends HandledCommand
{
    protected $signature = 'oms:rename-dubles';

    protected $description = 'la la la la la la a';

    protected string $period = 'Вручную';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
//        if ($this->isProcessRunning()) {
//            return 0;
//        }
//
//        $this->startProcess();

        $payments = Payment::whereBetween('date', ['2025-07-01', '2025-09-30'])
                    ->where('created_by_user_id', 1)
                    ->where('payment_type_id', Payment::PAYMENT_TYPE_CASH)->get();

        $doubles = [];

        foreach ($payments as $payment) {
            if (in_array($payment->id, $doubles)) {
                continue;
            }

            $doublePayments = Payment::whereBetween('date', ['2025-07-01', '2025-09-30'])
                ->where('created_by_user_id', 1)
                ->where('payment_type_id', Payment::PAYMENT_TYPE_CASH)
                ->where('id', '!=', $payment->id)
                ->where('description', $payment->description)
                ->where('amount', $payment->amount)
                ->where('code', $payment->code)
                ->get();

           foreach ($doublePayments as $doublePayment) {
               $doubles[] = $doublePayment->id;
               $doublePayment->delete();
           }
        }

//        $this->endProcess();

        dd('done');

        return 0;
    }
}
