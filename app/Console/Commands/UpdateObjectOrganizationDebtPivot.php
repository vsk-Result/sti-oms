<?php

namespace App\Console\Commands;

use App\Console\HandledCommand;
use App\Services\DebtService;
use Illuminate\Support\Facades\Cache;

class UpdateObjectOrganizationDebtPivot extends HandledCommand
{
    protected $signature = 'oms:update-object-organization-debt-pivot';

    protected $description = 'Обновляет сводную по долгам в разрезе объектов и организаций';

    protected string $period = 'Ежедневно каждые 30 минут';

    public function __construct(private DebtService $debtService)
    {
        parent::__construct();
    }

    public function handle()
    {
        if ($this->isProcessRunning()) {
            return 0;
        }

        $this->startProcess();

        $debtService = $this->debtService;

        Cache::put('object_debts_pivot_1', $debtService->getPivot());

        $this->sendInfoMessage('Обновление сводной завершено');

        $this->endProcess();

        return 0;
    }
}
