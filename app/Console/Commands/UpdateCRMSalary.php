<?php

namespace App\Console\Commands;

use App\Services\CRM\CalculateSalaryService;
use App\Services\CRONProcessService;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class UpdateCRMSalary extends Command
{
    protected $signature = 'oms:update-crm-salary';

    protected $description = 'Обновляет зарплаты из CRM';

    private CalculateSalaryService $salaryService;

    public function __construct(CRONProcessService $CRONProcessService, CalculateSalaryService $salaryService)
    {
        parent::__construct();
        $this->CRONProcessService = $CRONProcessService;
        $this->CRONProcessService->createProcess(
            $this->signature,
            $this->description,
            'Ежедневно в 12:00 и в 19:00'
        );
        $this->salaryService = $salaryService;
    }

    public function handle()
    {
        Log::channel('custom_imports_log')->debug('-----------------------------------------------------');
        Log::channel('custom_imports_log')->debug('[DATETIME] ' . Carbon::now()->format('d.m.Y H:i:s'));
        Log::channel('custom_imports_log')->debug('[START] Обновляет зарплаты из CRM');

        $this->salaryService->calculate();

        Log::channel('custom_imports_log')->debug('[SUCCESS] Данные успешно обновлены');
        $this->CRONProcessService->successProcess($this->signature);

        return 0;
    }
}
