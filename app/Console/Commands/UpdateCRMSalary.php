<?php

namespace App\Console\Commands;

use App\Console\HandledCommand;
use App\Services\CRM\CalculateSalaryService;

class UpdateCRMSalary extends HandledCommand
{
    protected $signature = 'oms:update-crm-salary';

    protected $description = 'Обновляет зарплаты из CRM';

    protected string $period = 'Ежедневно в 12:00 и в 19:00';

    public function __construct(private CalculateSalaryService $salaryService)
    {
        parent::__construct();
    }

    public function handle()
    {
        if ($this->isProcessRunning()) {
            return 0;
        }

        $this->startProcess();

        $this->salaryService->calculate();

        $this->sendInfoMessage('Данные успешно обновлены');

        $this->endProcess();

        return 0;
    }
}
