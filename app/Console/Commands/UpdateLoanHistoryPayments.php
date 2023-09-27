<?php

namespace App\Console\Commands;

use App\Models\Loan;
use App\Services\CRONProcessService;
use App\Services\LoanHistoryService;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class UpdateLoanHistoryPayments extends Command
{
    protected $signature = 'oms:update-loans-history-payments';

    protected $description = 'Обновляет историю оплат по номеру займа/кредита';

    private LoanHistoryService $loanHistoryService;

    public function __construct(LoanHistoryService $loanHistoryService, CRONProcessService $CRONProcessService)
    {
        parent::__construct();
        $this->CRONProcessService = $CRONProcessService;
        $this->CRONProcessService->createProcess(
            $this->signature,
            $this->description,
            'Ежедневно в 13:00 и в 18:00'
        );
        $this->loanHistoryService = $loanHistoryService;
    }

    public function handle()
    {
        Log::channel('custom_imports_log')->debug('-----------------------------------------------------');
        Log::channel('custom_imports_log')->debug('[DATETIME] ' . Carbon::now()->format('d.m.Y H:i:s'));
        Log::channel('custom_imports_log')->debug('[START] Обновление истории оплат займов/кредитов');

        try {
            $loans = Loan::get();
            foreach ($loans as $loan) {
                if (!$loan->is_auto_paid) {
                    continue;
                }

                $this->loanHistoryService->reloadLoanHistory($loan);
                Log::channel('custom_imports_log')->debug('[SUCCESS] Список оплат успешно обновлен [' . $loan->name . ']');
            }
        } catch (\Exception $e) {
            $errorMessage = '[ERROR] Не удалось обновить список оплат: "' . $e->getMessage();
            Log::channel('custom_imports_log')->debug($errorMessage);
            $this->CRONProcessService->failedProcess($this->signature, $errorMessage);
            return 0;
        }

        Log::channel('custom_imports_log')->debug('[SUCCESS] Все оплаты успешно обновлены');
        $this->CRONProcessService->successProcess($this->signature);

        return 0;
    }
}
