<?php

namespace App\Console\Commands;

use App\Services\CRONProcessService;
use App\Services\PivotObjectDebtService;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class UpdateDebtsPivotTable extends Command
{
    protected $signature = 'oms:update-debts-pivot-table';

    protected $description = 'Обновляет сводную таблицу по долгам объектов (поставщики, подрячики, услуги)';

    private PivotObjectDebtService $pivotObjectDebtService;

    public function __construct(CRONProcessService $CRONProcessService, PivotObjectDebtService $pivotObjectDebtService)
    {
        parent::__construct();
        $this->CRONProcessService = $CRONProcessService;
        $this->CRONProcessService->createProcess(
            $this->signature,
            $this->description,
            'Каждые 10 минут'
        );
        $this->pivotObjectDebtService = $pivotObjectDebtService;
    }

    public function handle()
    {
        Log::channel('custom_imports_log')->debug('-----------------------------------------------------');
        Log::channel('custom_imports_log')->debug('[DATETIME] ' . Carbon::now()->format('d.m.Y H:i:s'));
        Log::channel('custom_imports_log')->debug('[START] Обновление сводной таблицы по долгам объектов (поставщики, подрячики, услуги)');

        $this->pivotObjectDebtService->updatePivotDebtForAllObjects();

        Log::channel('custom_imports_log')->debug('[SUCCESS] Данные успешно обновлены');
        $this->CRONProcessService->successProcess($this->signature);

        return 0;
    }
}
