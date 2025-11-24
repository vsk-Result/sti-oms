<?php

namespace App\Console\Commands;

use App\Console\HandledCommand;
use App\Services\CashAccount\Payment\PaymentService;
use App\Services\CashAccount\Payment\PaymentValidateService;

class AutoChangeStatusesInCashAccountPayments extends HandledCommand
{
    protected $signature = 'oms:auto-change-statuses-in-cash-account-payments';

    protected $description = 'Автоматически меняет статус оплат в кассах на валидный к переносу на объект';

    protected string $period = 'Ежедневно в 21:30';

    public function __construct(
        private PaymentService $paymentService,
        private PaymentValidateService $paymentValidateService,
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
            $payments = $this->paymentService->getPaymentsToAutoValid();
            foreach ($payments as $payment) {
                $this->paymentValidateService->validate($payment, ['valid' => 'true']);
            }
        } catch (\Exception $e) {
            $this->sendErrorMessage('Не удалось перевести оплату в валидный статус');
            $this->sendErrorMessage($e->getMessage());
            $this->endProcess();
            return 0;
        }

        $this->sendInfoMessage('Оплаты успешно переведены в валидный статус');

        $this->endProcess();

        return 0;
    }
}
