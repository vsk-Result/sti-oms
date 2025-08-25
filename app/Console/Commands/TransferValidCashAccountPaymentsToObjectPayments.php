<?php

namespace App\Console\Commands;

use App\Console\HandledCommand;
use App\Services\CashAccount\CashAccountService;
use App\Services\CashAccount\Payment\PaymentTransferToObjectService;
use App\Services\CashAccount\Payment\PaymentValidateService;

class TransferValidCashAccountPaymentsToObjectPayments extends HandledCommand
{
    protected $signature = 'oms:transfer-valid-cash-account-payments-to-object-payments';

    protected $description = 'Переносит подтвержденные оплаты из касс в оплаты объектов';

    protected string $period = 'Ежедневно в 22:00';

    public function __construct(
        private CashAccountService $cashAccountService,
        private PaymentValidateService $paymentValidateService,
        private PaymentTransferToObjectService $paymentTransferToObjectService,
    ) {
        parent::__construct();
    }

    public function handle()
    {
        if ($this->isProcessRunning()) {
            return 0;
        }

        $this->startProcess();

        try {
            $cashAccounts = $this->cashAccountService->getCashAccountsWithValidPayments();
            foreach ($cashAccounts as $cashAccount) {
                $payments = $this->paymentValidateService->getActiveValidPayments($cashAccount);

                foreach ($payments as $payment) {
                    $this->paymentTransferToObjectService->transfer($payment);
                }
            }
        } catch (\Exception $e) {
            $this->sendErrorMessage('Не удалось перевести оплату из кассы на объект');
            $this->sendErrorMessage($e->getMessage());
            $this->endProcess();
            return 0;
        }

        $this->sendInfoMessage('Проверенные оплаты успешно переведены на объекты');

        $this->endProcess();

        return 0;
    }
}
