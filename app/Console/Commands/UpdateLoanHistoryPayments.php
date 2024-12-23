<?php

namespace App\Console\Commands;

use App\Console\HandledCommand;
use App\Models\Loan;
use App\Services\LoanHistoryService;

class UpdateLoanHistoryPayments extends HandledCommand
{
    protected $signature = 'oms:update-loans-history-payments';

    protected $description = 'Обновляет историю оплат по номеру займа/кредита';

    protected string $period = 'Ежедневно в 13:00 и в 18:00';

    public function __construct(private LoanHistoryService $loanHistoryService)
    {
        parent::__construct();
    }

    public function handle()
    {
        if ($this->isProcessRunning()) {
            return 0;
        }

        $this->startProcess();

        try {
            $loans = Loan::get();
            foreach ($loans as $loan) {
                if (!$loan->is_auto_paid) {
                    continue;
                }

                $this->loanHistoryService->reloadLoanHistory($loan);
                $this->sendInfoMessage('Список оплат успешно обновлен [' . $loan->name . ']');
            }
        } catch (\Exception $e) {
            $this->sendErrorMessage('Не удалось обновить список оплат: "' . $e->getMessage());
            $this->endProcess();
            return 0;
        }

        $this->sendInfoMessage('Все оплаты успешно обновлены');

        $this->endProcess();

        return 0;
    }
}
