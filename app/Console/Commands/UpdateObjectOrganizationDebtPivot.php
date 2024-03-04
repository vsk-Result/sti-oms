<?php

namespace App\Console\Commands;

use App\Services\CRONProcessService;
use App\Services\DebtService;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class UpdateObjectOrganizationDebtPivot extends Command
{
    protected $signature = 'oms:update-object-organization-debt-pivot';

    protected $description = 'Обновляет сводную по долгам в разрезе объектов и организаций';

    private DebtService $debtService;

    public function __construct(CRONProcessService $CRONProcessService, DebtService $debtService)
    {
        parent::__construct();
        $this->CRONProcessService = $CRONProcessService;
        $this->CRONProcessService->createProcess(
            $this->signature,
            $this->description,
            'Ежедневно каждые 30 минут'
        );
        $this->debtService = $debtService;
    }

    public function handle()
    {
        Log::channel('custom_imports_log')->debug('-----------------------------------------------------');
        Log::channel('custom_imports_log')->debug('[DATETIME] ' . Carbon::now()->format('d.m.Y H:i:s'));
        Log::channel('custom_imports_log')->debug('[START] Обновление сводной по долгам в разрезе объектов и организаций');

        $debtService = $this->debtService;

        Cache::put('object_debts_pivot', $debtService->getPivot());

        Log::channel('custom_imports_log')->debug('[SUCCESS] Обновление сводной завершено');
        $this->CRONProcessService->successProcess($this->signature);

        return 0;
    }
}
