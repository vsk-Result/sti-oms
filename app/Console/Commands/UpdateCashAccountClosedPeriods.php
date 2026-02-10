<?php

namespace App\Console\Commands;

use App\Console\HandledCommand;
use App\Models\CashAccount\CashAccount;
use App\Models\CashAccount\CashAccountPayment;
use App\Services\CashAccount\Payment\PaymentTransferToObjectService;

class UpdateCashAccountClosedPeriods extends HandledCommand
{
    protected $signature = 'oms:update-cash-account-closed-periods';

    protected $description = 'Обновляет оплаты на объектах в закрытых периодах касс';

    protected string $period = 'Ежедневно в 9:00 и в 16:00';

    public function __construct(private PaymentTransferToObjectService $paymentTransferToObjectService,)
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

        $this->sendInfoMessage('Старт обновления');

        $cashAccounts = CashAccount::active()->get();

        foreach ($cashAccounts as $cashAccount) {
            foreach ($cashAccount->closePeriods as $closePeriod) {
                $closedPayments = CashAccountPayment::where('cash_account_id', $cashAccount->id)
                    ->whereIn('status_id', [CashAccountPayment::STATUS_CLOSED])
                    ->where('date', 'LIKE', substr($closePeriod->period, 0, 7) . '-%')
                    ->get();

                foreach ($closedPayments as $payment) {
                    $data = $payment->getObjectPaymentData();

                    if (is_null($data['object_payment_id'])) {
                        $this->paymentTransferToObjectService->transfer($payment);
                    }
                }
            }

        }

        $this->sendInfoMessage('Обновление завершено');

//        $this->endProcess();

        return 0;
    }
}
